<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(12345); // Deterministic

        $products = Product::all();
        $locations = Location::all();

        foreach ($products as $product) {
            foreach ($locations as $location) {
                // High-velocity products: low stock to create stockout risk
                if (str_starts_with($product->sku, 'HV-')) {
                    $onHand = mt_rand(5, 30); // Very low stock
                    $onOrder = mt_rand(0, 20);
                    $ageDays = mt_rand(0, 30); // Fresh inventory
                }
                // Dead stock: high inventory, old age
                elseif (str_starts_with($product->sku, 'DS-')) {
                    $onHand = mt_rand(150, 400); // Way too much
                    $onOrder = 0;
                    $ageDays = mt_rand(130, 250); // Very old
                }
                // Low margin: moderate stock
                elseif (str_starts_with($product->sku, 'LM-')) {
                    $onHand = mt_rand(20, 80);
                    $onOrder = mt_rand(0, 30);
                    $ageDays = mt_rand(15, 60);
                }
                // Regular products: varied inventory
                else {
                    // 70% have some stock
                    if (mt_rand(1, 100) <= 70) {
                        $onHand = mt_rand(10, 200);
                        $onOrder = mt_rand(0, 50);
                        $ageDays = mt_rand(0, 120);
                    } else {
                        // 30% out of stock or very low
                        $onHand = mt_rand(0, 5);
                        $onOrder = mt_rand(0, 100);
                        $ageDays = mt_rand(0, 60);
                    }
                }

                Inventory::create([
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'on_hand' => $onHand,
                    'on_order' => $onOrder,
                    'inventory_age_days' => $ageDays,
                ]);
            }
        }
    }
}
