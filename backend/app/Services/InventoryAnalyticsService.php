<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\SalesTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryAnalyticsService
{
    private const EPSILON = 0.001;
    private const SAFETY_STOCK_DEFAULT = 14;
    private const SAFETY_STOCK_HIGH_VELOCITY = 21;
    private const TARGET_COVERAGE_DAYS = 30;
    private const DEAD_STOCK_AGE_THRESHOLD = 120;
    private const DEAD_STOCK_VELOCITY_THRESHOLD = 0.05;

    /**
     * Calculate sales velocity for a product at a location
     */
    public function calculateVelocity(int $productId, int $locationId, int $days = 30): float
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($days);

        $totalSold = SalesTransaction::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('units_sold');

        return $totalSold / $days;
    }

    /**
     * Calculate days on hand for inventory
     */
    public function calculateDaysOnHand(int $onHand, float $velocity): float
    {
        if ($velocity < self::EPSILON) {
            return 999999; // Effectively infinite
        }

        return $onHand / $velocity;
    }

    /**
     * Get safety stock days for a product
     */
    public function getSafetyStockDays(int $productId, float $velocity): int
    {
        // Top velocity products get extra safety stock
        $highVelocityThreshold = $this->getHighVelocityThreshold();

        if ($velocity >= $highVelocityThreshold) {
            return self::SAFETY_STOCK_HIGH_VELOCITY;
        }

        return self::SAFETY_STOCK_DEFAULT;
    }

    /**
     * Calculate reorder point in units
     */
    public function calculateReorderPoint(float $velocity, int $leadTimeDays, int $safetyStockDays): int
    {
        return (int) ceil($velocity * ($leadTimeDays + $safetyStockDays));
    }

    /**
     * Calculate suggested reorder quantity
     */
    public function calculateSuggestedReorderQty(
        float $velocity,
        int $leadTimeDays,
        int $onHand,
        int $onOrder
    ): int {
        $targetCoverage = $leadTimeDays + self::TARGET_COVERAGE_DAYS;
        $targetInventory = $velocity * $targetCoverage;
        $currentAndPipeline = $onHand + $onOrder;

        $needed = $targetInventory - $currentAndPipeline;

        return max(0, (int) ceil($needed));
    }

    /**
     * Check if inventory has stockout risk
     */
    public function hasStockoutRisk(float $daysOnHand, int $leadTimeDays, int $safetyStockDays): array
    {
        $threshold = $leadTimeDays + $safetyStockDays;

        if ($daysOnHand < $threshold) {
            $severity = $daysOnHand < $leadTimeDays ? 'critical' : 'warning';
            return [
                'has_risk' => true,
                'severity' => $severity,
                'threshold' => $threshold,
            ];
        }

        return ['has_risk' => false];
    }

    /**
     * Check if inventory is dead stock
     */
    public function isDeadStock(int $inventoryAgeDays, float $velocity, int $onHand): array
    {
        if ($inventoryAgeDays > self::DEAD_STOCK_AGE_THRESHOLD &&
            $velocity < self::DEAD_STOCK_VELOCITY_THRESHOLD &&
            $onHand > 0) {
            return [
                'is_dead_stock' => true,
                'reason' => 'Low velocity + aged inventory',
                'age_days' => $inventoryAgeDays,
                'velocity' => round($velocity, 3),
            ];
        }

        return ['is_dead_stock' => false];
    }

    /**
     * Get enriched inventory data with all analytics
     */
    public function getEnrichedInventory(?int $locationId = null, ?int $supplierId = null, ?array $flags = []): array
    {
        $query = Inventory::with(['product.supplier', 'location'])
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->select('inventory.*');

        if ($locationId) {
            $query->where('inventory.location_id', $locationId);
        }

        if ($supplierId) {
            $query->where('products.supplier_id', $supplierId);
        }

        $inventoryItems = $query->get();
        $enriched = [];

        foreach ($inventoryItems as $item) {
            $velocity = $this->calculateVelocity($item->product_id, $item->location_id);
            $daysOnHand = $this->calculateDaysOnHand($item->on_hand, $velocity);
            $leadTimeDays = $item->product->supplier->lead_time_days;
            $safetyStockDays = $this->getSafetyStockDays($item->product_id, $velocity);
            $reorderPoint = $this->calculateReorderPoint($velocity, $leadTimeDays, $safetyStockDays);
            $suggestedQty = $this->calculateSuggestedReorderQty($velocity, $leadTimeDays, $item->on_hand, $item->on_order);
            $stockoutRisk = $this->hasStockoutRisk($daysOnHand, $leadTimeDays, $safetyStockDays);
            $deadStock = $this->isDeadStock($item->inventory_age_days, $velocity, $item->on_hand);

            $margin = $item->product->unit_price - $item->product->unit_cost;
            $marginPercent = $item->product->unit_price > 0
                ? ($margin / $item->product->unit_price) * 100
                : 0;

            $data = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'location_id' => $item->location_id,
                'sku' => $item->product->sku,
                'product_name' => $item->product->name,
                'category' => $item->product->category,
                'location_name' => $item->location->name,
                'supplier_name' => $item->product->supplier->name,
                'supplier_id' => $item->product->supplier->id,
                'on_hand' => $item->on_hand,
                'on_order' => $item->on_order,
                'unit_cost' => (float) $item->product->unit_cost,
                'unit_price' => (float) $item->product->unit_price,
                'margin' => round($margin, 2),
                'margin_percent' => round($marginPercent, 2),
                'velocity' => round($velocity, 2),
                'days_on_hand' => $daysOnHand > 999 ? 999 : round($daysOnHand, 1),
                'lead_time_days' => $leadTimeDays,
                'safety_stock_days' => $safetyStockDays,
                'reorder_point' => $reorderPoint,
                'suggested_reorder_qty' => $suggestedQty,
                'stockout_risk' => $stockoutRisk,
                'dead_stock' => $deadStock,
                'inventory_age_days' => $item->inventory_age_days,
            ];

            // Apply flag filters if requested
            if (!empty($flags)) {
                $include = false;
                if (in_array('stockout', $flags) && $stockoutRisk['has_risk']) {
                    $include = true;
                }
                if (in_array('dead_stock', $flags) && $deadStock['is_dead_stock']) {
                    $include = true;
                }
                if (in_array('low_margin', $flags) && $marginPercent < 20) {
                    $include = true;
                }
                if (!$include) {
                    continue;
                }
            }

            $enriched[] = $data;
        }

        return $enriched;
    }

    /**
     * Get top N products by velocity
     */
    private function getHighVelocityThreshold(): float
    {
        // Calculate the 90th percentile velocity across all products
        $velocities = [];

        $products = Product::all();
        $locations = \App\Models\Location::all();

        foreach ($products as $product) {
            foreach ($locations as $location) {
                $velocity = $this->calculateVelocity($product->id, $location->id);
                if ($velocity > 0) {
                    $velocities[] = $velocity;
                }
            }
        }

        if (empty($velocities)) {
            return 10; // Default fallback
        }

        rsort($velocities);
        $index = (int) floor(count($velocities) * 0.1); // Top 10%

        return $velocities[$index] ?? 10;
    }
}
