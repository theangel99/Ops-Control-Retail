<?php

namespace App\Http\Controllers;

use App\Services\InventoryAnalyticsService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    private InventoryAnalyticsService $inventoryService;

    public function __construct(InventoryAnalyticsService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $locationId = $request->query('location_id');
        $supplierId = $request->query('supplier_id');
        $flags = $request->query('flags');

        if ($locationId === 'all' || $locationId === null) {
            $locationId = null;
        }

        if ($supplierId === 'all' || $supplierId === null) {
            $supplierId = null;
        }

        if (is_string($flags)) {
            $flags = explode(',', $flags);
        } else if (!is_array($flags)) {
            $flags = [];
        }

        $data = $this->inventoryService->getEnrichedInventory($locationId, $supplierId, $flags);

        return response()->json(['data' => $data]);
    }
}
