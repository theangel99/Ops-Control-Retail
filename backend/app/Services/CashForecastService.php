<?php

namespace App\Services;

use App\Models\CashEvent;
use App\Models\CashSettings;
use App\Models\PurchaseOrder;
use App\Models\SalesTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashForecastService
{
    /**
     * Get cash forecast for specified periods
     */
    public function getForecast(array $periods = [30, 60, 90]): array
    {
        $settings = CashSettings::first();

        if (!$settings) {
            return [
                'current_cash' => 0,
                'projections' => [],
            ];
        }

        $currentCash = $this->calculateCurrentCash($settings);

        $projections = [];
        foreach ($periods as $days) {
            $projections[$days] = $this->projectCashForDays($currentCash, $settings, $days);
        }

        return [
            'current_cash' => round($currentCash, 2),
            'projections' => $projections,
            'low_water_mark' => $this->findLowWaterMark($currentCash, $settings, max($periods)),
        ];
    }

    /**
     * Calculate current cash position
     */
    private function calculateCurrentCash(CashSettings $settings): float
    {
        $cash = $settings->starting_cash;

        // Add all cash events
        $events = CashEvent::where('date', '<=', Carbon::today())->get();

        foreach ($events as $event) {
            if ($event->type === 'inflow') {
                $cash += $event->amount;
            } else {
                $cash -= $event->amount;
            }
        }

        return $cash;
    }

    /**
     * Project cash position for N days in the future
     */
    private function projectCashForDays(float $startingCash, CashSettings $settings, int $days): array
    {
        $cash = $startingCash;
        $today = Carbon::today();
        $targetDate = $today->copy()->addDays($days);

        // Get future inflows (revenue collections)
        $inflows = $this->getProjectedInflows($today, $targetDate, $settings);

        // Get future outflows (PO payments)
        $outflows = $this->getProjectedOutflows($today, $targetDate, $settings);

        $totalInflows = array_sum(array_column($inflows, 'amount'));
        $totalOutflows = array_sum(array_column($outflows, 'amount'));

        $projectedCash = $cash + $totalInflows - $totalOutflows;

        return [
            'date' => $targetDate->format('Y-m-d'),
            'projected_cash' => round($projectedCash, 2),
            'total_inflows' => round($totalInflows, 2),
            'total_outflows' => round($totalOutflows, 2),
        ];
    }

    /**
     * Get projected cash inflows from sales
     */
    private function getProjectedInflows(Carbon $startDate, Carbon $endDate, CashSettings $settings): array
    {
        $collectionDelay = $settings->revenue_collection_delay_days;
        $inflows = [];

        // Sales that will be collected in this period
        $salesStartDate = $startDate->copy()->subDays($collectionDelay);
        $salesEndDate = $endDate->copy()->subDays($collectionDelay);

        $sales = SalesTransaction::whereBetween('date', [$salesStartDate, $salesEndDate])->get();

        foreach ($sales as $sale) {
            $collectionDate = Carbon::parse($sale->date)->addDays($collectionDelay);

            if ($collectionDate->between($startDate, $endDate)) {
                $inflows[] = [
                    'date' => $collectionDate->format('Y-m-d'),
                    'amount' => (float) $sale->revenue,
                    'type' => 'revenue_collection',
                ];
            }
        }

        return $inflows;
    }

    /**
     * Get projected cash outflows from purchase orders
     */
    private function getProjectedOutflows(Carbon $startDate, Carbon $endDate, CashSettings $settings): array
    {
        $paymentTerms = $settings->payment_terms_days;
        $outflows = [];

        // Get approved/ordered POs that will come due
        $pos = PurchaseOrder::whereIn('status', ['approved', 'ordered'])
            ->whereNotNull('ordered_at')
            ->get();

        foreach ($pos as $po) {
            $paymentDate = Carbon::parse($po->ordered_at)->addDays($paymentTerms);

            if ($paymentDate->between($startDate, $endDate)) {
                $outflows[] = [
                    'date' => $paymentDate->format('Y-m-d'),
                    'amount' => (float) $po->total_cost,
                    'type' => 'po_payment',
                    'po_number' => $po->po_number,
                ];
            }
        }

        // Include future cash events
        $events = CashEvent::whereBetween('date', [$startDate, $endDate])
            ->where('type', 'outflow')
            ->get();

        foreach ($events as $event) {
            $outflows[] = [
                'date' => $event->date->format('Y-m-d'),
                'amount' => (float) $event->amount,
                'type' => 'scheduled_outflow',
            ];
        }

        return $outflows;
    }

    /**
     * Find the lowest projected cash point in the period
     */
    private function findLowWaterMark(float $startingCash, CashSettings $settings, int $days): array
    {
        $cash = $startingCash;
        $lowWaterMark = $cash;
        $lowWaterMarkDate = Carbon::today()->format('Y-m-d');

        $today = Carbon::today();
        $endDate = $today->copy()->addDays($days);

        // Build a daily cash flow timeline
        $dailyCashFlow = [];

        // Get all inflows
        $inflows = $this->getProjectedInflows($today, $endDate, $settings);
        foreach ($inflows as $inflow) {
            if (!isset($dailyCashFlow[$inflow['date']])) {
                $dailyCashFlow[$inflow['date']] = 0;
            }
            $dailyCashFlow[$inflow['date']] += $inflow['amount'];
        }

        // Get all outflows
        $outflows = $this->getProjectedOutflows($today, $endDate, $settings);
        foreach ($outflows as $outflow) {
            if (!isset($dailyCashFlow[$outflow['date']])) {
                $dailyCashFlow[$outflow['date']] = 0;
            }
            $dailyCashFlow[$outflow['date']] -= $outflow['amount'];
        }

        // Sort by date and calculate running cash balance
        ksort($dailyCashFlow);

        foreach ($dailyCashFlow as $date => $netFlow) {
            $cash += $netFlow;

            if ($cash < $lowWaterMark) {
                $lowWaterMark = $cash;
                $lowWaterMarkDate = $date;
            }
        }

        return [
            'amount' => round($lowWaterMark, 2),
            'date' => $lowWaterMarkDate,
        ];
    }

    /**
     * Record cash event when PO status changes
     */
    public function recordPOCashEvent(PurchaseOrder $po): void
    {
        if ($po->status === 'approved' && $po->ordered_at) {
            $settings = CashSettings::first();
            $paymentDate = Carbon::parse($po->ordered_at)->addDays($settings->payment_terms_days);

            CashEvent::updateOrCreate(
                [
                    'reference_type' => 'purchase_order',
                    'reference_id' => $po->id,
                ],
                [
                    'date' => $paymentDate,
                    'type' => 'outflow',
                    'amount' => $po->total_cost,
                    'description' => "Payment for PO {$po->po_number}",
                ]
            );
        }
    }
}
