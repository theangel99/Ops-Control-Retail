<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $locationId = $request->query('location_id');

        if ($locationId === 'all' || $locationId === null) {
            $locationId = null;
        }

        $data = $this->dashboardService->getExecutiveDashboard($locationId);

        return response()->json($data);
    }
}
