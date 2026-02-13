<?php

namespace App\Services;

use App\Models\SalesTransaction;
use App\Models\Inventory;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private InventoryAnalyticsService $inventoryService;
    private CashForecastService $cashService;

    public function __construct(
        InventoryAnalyticsService $inventoryService,
        CashForecastService $cashService
    ) {
        $this->inventoryService = $inventoryService;
        $this->cashService = $cashService;
    }

    /**
     * Get executive dashboard data
     */
    public function getExecutiveDashboard(?int $locationId = null): array
    {
        return [
            'kpis' => $this->getKPIs($locationId),
            'charts' => $this->getCharts($locationId),
            'top_lists' => $this->getTopLists($locationId),
        ];
    }

    /**
     * Get KPI tiles
     */
    private function getKPIs(?int $locationId): array
    {
        $endDate = Carbon::now();
        $startDate30 = $endDate->copy()->subDays(30);

        // Revenue (30d)
        $revenueQuery = SalesTransaction::whereBetween('date', [$startDate30, $endDate]);
        if ($locationId) {
            $revenueQuery->where('location_id', $locationId);
        }
        $revenue30d = $revenueQuery->sum('revenue');

        // Gross margin (30d)
        $salesData = $revenueQuery->with('product')->get();
        $totalCost = 0;
        foreach ($salesData as $sale) {
            $totalCost += $sale->units_sold * $sale->product->unit_cost;
        }
        $grossMargin = $revenue30d - $totalCost;
        $grossMarginPercent = $revenue30d > 0 ? ($grossMargin / $revenue30d) * 100 : 0;

        // Cash forecast
        $cashForecast = $this->cashService->getForecast();

        // Stockout risk count
        $enrichedInventory = $this->inventoryService->getEnrichedInventory($locationId);
        $stockoutCount = count(array_filter($enrichedInventory, function ($item) {
            return $item['stockout_risk']['has_risk'] ?? false;
        }));

        // Dead stock value
        $deadStockValue = 0;
        foreach ($enrichedInventory as $item) {
            if ($item['dead_stock']['is_dead_stock'] ?? false) {
                $deadStockValue += $item['on_hand'] * $item['unit_cost'];
            }
        }

        // Inventory turnover (annual estimate)
        $revenue12m = SalesTransaction::whereBetween('date', [
            $endDate->copy()->subDays(365),
            $endDate
        ])->sum('revenue');

        $avgInventoryValue = Inventory::with('product')->get()->sum(function ($inv) {
            return $inv->on_hand * $inv->product->unit_cost;
        });

        $inventoryTurnover = $avgInventoryValue > 0 ? $revenue12m / $avgInventoryValue : 0;

        return [
            'revenue_30d' => round($revenue30d, 2),
            'gross_margin_30d' => round($grossMargin, 2),
            'gross_margin_percent' => round($grossMarginPercent, 2),
            'current_cash' => $cashForecast['current_cash'],
            'cash_30d' => $cashForecast['projections'][30]['projected_cash'] ?? 0,
            'cash_60d' => $cashForecast['projections'][60]['projected_cash'] ?? 0,
            'cash_90d' => $cashForecast['projections'][90]['projected_cash'] ?? 0,
            'stockout_risk_count' => $stockoutCount,
            'dead_stock_value' => round($deadStockValue, 2),
            'inventory_turnover' => round($inventoryTurnover, 2),
        ];
    }

    /**
     * Get chart data
     */
    private function getCharts(?int $locationId): array
    {
        return [
            'revenue_trend' => $this->getRevenueTrend($locationId),
            'stockout_risk_trend' => $this->getStockoutRiskTrend($locationId),
            'dead_stock_trend' => $this->getDeadStockTrend($locationId),
        ];
    }

    /**
     * Get 12-month revenue trend
     */
    private function getRevenueTrend(?int $locationId): array
    {
        $months = [];
        $endDate = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $monthStart = $endDate->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $query = SalesTransaction::whereBetween('date', [$monthStart, $monthEnd]);
            if ($locationId) {
                $query->where('location_id', $locationId);
            }

            $revenue = $query->sum('revenue');

            $months[] = [
                'month' => $monthStart->format('M Y'),
                'revenue' => round($revenue, 2),
            ];
        }

        return $months;
    }

    /**
     * Get stockout risk trend
     */
    private function getStockoutRiskTrend(?int $locationId): array
    {
        // Simplified: current stockout count by severity
        $enrichedInventory = $this->inventoryService->getEnrichedInventory($locationId);

        $critical = 0;
        $warning = 0;

        foreach ($enrichedInventory as $item) {
            if ($item['stockout_risk']['has_risk'] ?? false) {
                if (($item['stockout_risk']['severity'] ?? '') === 'critical') {
                    $critical++;
                } else {
                    $warning++;
                }
            }
        }

        return [
            'critical' => $critical,
            'warning' => $warning,
        ];
    }

    /**
     * Get dead stock trend
     */
    private function getDeadStockTrend(?int $locationId): array
    {
        $enrichedInventory = $this->inventoryService->getEnrichedInventory($locationId);

        $totalValue = 0;
        $count = 0;

        foreach ($enrichedInventory as $item) {
            if ($item['dead_stock']['is_dead_stock'] ?? false) {
                $totalValue += $item['on_hand'] * $item['unit_cost'];
                $count++;
            }
        }

        return [
            'total_value' => round($totalValue, 2),
            'sku_count' => $count,
        ];
    }

    /**
     * Get top lists
     */
    private function getTopLists(?int $locationId): array
    {
        $enrichedInventory = $this->inventoryService->getEnrichedInventory($locationId);

        // Highest stockout risk
        $stockoutRisk = array_filter($enrichedInventory, function ($item) {
            return $item['stockout_risk']['has_risk'] ?? false;
        });

        usort($stockoutRisk, function ($a, $b) {
            return $a['days_on_hand'] <=> $b['days_on_hand'];
        });

        $topStockoutRisk = array_slice($stockoutRisk, 0, 10);

        // Largest dead stock exposure
        $deadStock = array_filter($enrichedInventory, function ($item) {
            return $item['dead_stock']['is_dead_stock'] ?? false;
        });

        usort($deadStock, function ($a, $b) {
            $valueA = $a['on_hand'] * $a['unit_cost'];
            $valueB = $b['on_hand'] * $b['unit_cost'];
            return $valueB <=> $valueA;
        });

        $topDeadStock = array_slice($deadStock, 0, 10);

        // Add dead stock value to each item
        foreach ($topDeadStock as &$item) {
            $item['dead_stock_value'] = round($item['on_hand'] * $item['unit_cost'], 2);
        }

        return [
            'top_stockout_risk' => array_values($topStockoutRisk),
            'top_dead_stock' => array_values($topDeadStock),
            'cash_low_water_mark' => $this->cashService->getForecast()['low_water_mark'],
        ];
    }
}
