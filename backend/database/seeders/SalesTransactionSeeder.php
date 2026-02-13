<?php

namespace Database\Seeders;

use App\Models\SalesTransaction;
use App\Models\Product;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalesTransactionSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(12345); // Deterministic

        $products = Product::all();
        $locations = Location::where('is_warehouse', false)->get(); // Only stores sell
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(365);

        // Seasonality multipliers by month (1-12)
        $seasonality = [
            1 => 0.8,  // Jan - slow
            2 => 0.7,  // Feb - slowest
            3 => 0.9,  // Mar - pickup
            4 => 1.0,  // Apr - normal
            5 => 1.1,  // May - spring boost
            6 => 1.2,  // Jun - summer start
            7 => 1.3,  // Jul - peak summer
            8 => 1.2,  // Aug - summer
            9 => 1.0,  // Sep - back to school
            10 => 1.1, // Oct - fall
            11 => 1.4, // Nov - Black Friday
            12 => 1.5, // Dec - Holiday peak
        ];

        foreach ($products as $product) {
            // Determine base daily velocity for this product
            if (str_starts_with($product->sku, 'HV-')) {
                $baseDailyVelocity = mt_rand(15, 35); // High velocity
            } elseif (str_starts_with($product->sku, 'DS-')) {
                $baseDailyVelocity = 0.02; // Almost no sales
            } elseif (str_starts_with($product->sku, 'LM-')) {
                $baseDailyVelocity = mt_rand(3, 8); // Moderate
            } else {
                // Random velocity for regular products
                $baseDailyVelocity = mt_rand(1, 100) / 10; // 0.1 to 10 units/day
            }

            foreach ($locations as $location) {
                // Each location has a variance factor
                $locationFactor = mt_rand(70, 130) / 100; // 0.7 to 1.3

                $currentDate = $startDate->copy();

                while ($currentDate <= $endDate) {
                    $month = $currentDate->month;
                    $seasonalFactor = $seasonality[$month];

                    // Calculate daily sales with randomness
                    $dailyVelocity = $baseDailyVelocity * $locationFactor * $seasonalFactor;
                    $dailyVelocity = $dailyVelocity * mt_rand(70, 130) / 100; // Daily variance

                    $unitsSold = max(0, (int) round($dailyVelocity));

                    if ($unitsSold > 0) {
                        $revenue = $unitsSold * $product->unit_price;

                        SalesTransaction::create([
                            'date' => $currentDate->toDateString(),
                            'product_id' => $product->id,
                            'location_id' => $location->id,
                            'units_sold' => $unitsSold,
                            'revenue' => $revenue,
                        ]);
                    }

                    $currentDate->addDay();
                }
            }
        }
    }
}
