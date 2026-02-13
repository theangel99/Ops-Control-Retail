<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Product;
use App\Models\Inventory;
use App\Services\CashForecastService;
use App\Services\InventoryAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    private CashForecastService $cashService;
    private InventoryAnalyticsService $inventoryService;

    public function __construct(
        CashForecastService $cashService,
        InventoryAnalyticsService $inventoryService
    ) {
        $this->cashService = $cashService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * List all purchase orders
     */
    public function index(Request $request)
    {
        $pos = PurchaseOrder::with(['supplier', 'location', 'lines.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $pos]);
    }

    /**
     * Get single PO with details
     */
    public function show($id)
    {
        $po = PurchaseOrder::with(['supplier', 'location', 'lines.product'])
            ->findOrFail($id);

        // Add cash impact
        $cashForecast = $this->cashService->getForecast();

        return response()->json([
            'data' => $po,
            'cash_impact' => $cashForecast,
        ]);
    }

    /**
     * Generate suggested PO
     */
    public function suggest(Request $request)
    {
        $locationId = $request->input('location_id');
        $productIds = $request->input('product_ids', []);

        if (empty($productIds)) {
            // Auto-select products with stockout risk
            $enrichedInventory = $this->inventoryService->getEnrichedInventory($locationId);

            $productsToOrder = array_filter($enrichedInventory, function ($item) {
                return ($item['stockout_risk']['has_risk'] ?? false) && $item['suggested_reorder_qty'] > 0;
            });

            $productIds = array_map(function ($item) {
                return $item['product_id'];
            }, $productsToOrder);
        }

        if (empty($productIds)) {
            return response()->json(['message' => 'No products need reordering'], 200);
        }

        // Group products by supplier
        $products = Product::whereIn('id', $productIds)->with('supplier')->get();
        $bySupplier = $products->groupBy('supplier_id');

        $createdPOs = [];

        foreach ($bySupplier as $supplierId => $supplierProducts) {
            $supplier = $supplierProducts->first()->supplier;
            $totalCost = 0;
            $lines = [];

            foreach ($supplierProducts as $product) {
                $inventory = Inventory::where('product_id', $product->id)
                    ->where('location_id', $locationId)
                    ->first();

                if (!$inventory) {
                    continue;
                }

                $velocity = $this->inventoryService->calculateVelocity($product->id, $locationId);
                $qty = $this->inventoryService->calculateSuggestedReorderQty(
                    $velocity,
                    $supplier->lead_time_days,
                    $inventory->on_hand,
                    $inventory->on_order
                );

                if ($qty <= 0) {
                    continue;
                }

                $lineCost = $qty * $product->unit_cost;
                $totalCost += $lineCost;

                $lines[] = [
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'unit_cost' => $product->unit_cost,
                ];
            }

            if (empty($lines)) {
                continue;
            }

            // Create PO
            $poNumber = 'PO-' . strtoupper(uniqid());
            $expectedDelivery = Carbon::now()->addDays($supplier->lead_time_days);

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $supplierId,
                'location_id' => $locationId,
                'status' => 'draft',
                'total_cost' => $totalCost,
                'expected_delivery_date' => $expectedDelivery,
            ]);

            foreach ($lines as $lineData) {
                PurchaseOrderLine::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $lineData['product_id'],
                    'qty' => $lineData['qty'],
                    'unit_cost' => $lineData['unit_cost'],
                ]);
            }

            $createdPOs[] = $po->load(['supplier', 'location', 'lines.product']);
        }

        return response()->json([
            'message' => 'Purchase orders created',
            'data' => $createdPOs,
        ]);
    }

    /**
     * Transition PO to next status
     */
    public function transition(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);
        $nextStatus = $request->input('next_status');

        $validTransitions = [
            'draft' => 'submitted',
            'submitted' => 'approved',
            'approved' => 'ordered',
            'ordered' => 'received',
        ];

        if (!isset($validTransitions[$po->status]) || $validTransitions[$po->status] !== $nextStatus) {
            return response()->json(['error' => 'Invalid status transition'], 400);
        }

        $po->status = $nextStatus;

        if ($nextStatus === 'ordered') {
            $po->ordered_at = Carbon::now();
            // Record cash event
            $this->cashService->recordPOCashEvent($po);
        }

        if ($nextStatus === 'received') {
            $po->received_at = Carbon::now();
            // Update inventory
            $this->receiveInventory($po);
        }

        $po->save();

        return response()->json([
            'message' => 'Status updated',
            'data' => $po->load(['supplier', 'location', 'lines.product']),
        ]);
    }

    /**
     * Receive inventory from PO
     */
    private function receiveInventory(PurchaseOrder $po)
    {
        foreach ($po->lines as $line) {
            $inventory = Inventory::where('product_id', $line->product_id)
                ->where('location_id', $po->location_id)
                ->first();

            if ($inventory) {
                $inventory->on_hand += $line->qty;
                $inventory->on_order = max(0, $inventory->on_order - $line->qty);
                $inventory->inventory_age_days = 0; // Reset age on new receipt
                $inventory->save();
            }
        }
    }
}
