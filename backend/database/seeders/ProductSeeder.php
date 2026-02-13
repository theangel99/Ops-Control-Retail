<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Set seed for deterministic data
        mt_srand(12345);

        $categories = ['Electronics', 'Apparel', 'Home Goods', 'Sports', 'Tools', 'Garden', 'Toys', 'Books'];
        $products = [];

        // First, create 10 high-velocity SKUs (stockout risk)
        $highVelocitySkus = [
            ['sku' => 'HV-001', 'name' => 'Premium Wireless Headphones', 'category' => 'Electronics', 'supplier_id' => 1, 'unit_cost' => 45.00, 'unit_price' => 89.99],
            ['sku' => 'HV-002', 'name' => 'Smart Fitness Tracker', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 30.00, 'unit_price' => 69.99],
            ['sku' => 'HV-003', 'name' => 'Yoga Mat Premium', 'category' => 'Sports', 'supplier_id' => 1, 'unit_cost' => 12.00, 'unit_price' => 29.99],
            ['sku' => 'HV-004', 'name' => 'LED Desk Lamp', 'category' => 'Home Goods', 'supplier_id' => 3, 'unit_cost' => 18.00, 'unit_price' => 44.99],
            ['sku' => 'HV-005', 'name' => 'Stainless Steel Water Bottle', 'category' => 'Sports', 'supplier_id' => 1, 'unit_cost' => 8.00, 'unit_price' => 24.99],
            ['sku' => 'HV-006', 'name' => 'Bluetooth Speaker Portable', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 25.00, 'unit_price' => 59.99],
            ['sku' => 'HV-007', 'name' => 'Running Shoes Pro', 'category' => 'Apparel', 'supplier_id' => 1, 'unit_cost' => 35.00, 'unit_price' => 89.99],
            ['sku' => 'HV-008', 'name' => 'Backpack Tactical', 'category' => 'Sports', 'supplier_id' => 3, 'unit_cost' => 22.00, 'unit_price' => 54.99],
            ['sku' => 'HV-009', 'name' => 'Phone Charging Cable 3-Pack', 'category' => 'Electronics', 'supplier_id' => 1, 'unit_cost' => 6.00, 'unit_price' => 19.99],
            ['sku' => 'HV-010', 'name' => 'Coffee Maker Single Serve', 'category' => 'Home Goods', 'supplier_id' => 2, 'unit_cost' => 40.00, 'unit_price' => 99.99],
        ];

        foreach ($highVelocitySkus as $product) {
            Product::create($product);
        }

        // Create 20 dead-stock SKUs (slow-moving, capital trapped)
        $deadStockSkus = [
            ['sku' => 'DS-001', 'name' => 'VHS Player Retro', 'category' => 'Electronics', 'supplier_id' => 1, 'unit_cost' => 50.00, 'unit_price' => 79.99],
            ['sku' => 'DS-002', 'name' => 'Floppy Disk Storage Box', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 15.00, 'unit_price' => 29.99],
            ['sku' => 'DS-003', 'name' => 'Slide Projector Classic', 'category' => 'Electronics', 'supplier_id' => 3, 'unit_cost' => 65.00, 'unit_price' => 129.99],
            ['sku' => 'DS-004', 'name' => 'Cassette Tape Organizer', 'category' => 'Home Goods', 'supplier_id' => 1, 'unit_cost' => 12.00, 'unit_price' => 24.99],
            ['sku' => 'DS-005', 'name' => 'Typewriter Ribbon Set', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 8.00, 'unit_price' => 15.99],
            ['sku' => 'DS-006', 'name' => 'Film Camera 35mm', 'category' => 'Electronics', 'supplier_id' => 1, 'unit_cost' => 85.00, 'unit_price' => 149.99],
            ['sku' => 'DS-007', 'name' => 'Pager Belt Clip', 'category' => 'Electronics', 'supplier_id' => 3, 'unit_cost' => 5.00, 'unit_price' => 9.99],
            ['sku' => 'DS-008', 'name' => 'Phone Book Stand', 'category' => 'Home Goods', 'supplier_id' => 2, 'unit_cost' => 18.00, 'unit_price' => 34.99],
            ['sku' => 'DS-009', 'name' => 'Rolodex Card Set', 'category' => 'Home Goods', 'supplier_id' => 1, 'unit_cost' => 10.00, 'unit_price' => 19.99],
            ['sku' => 'DS-010', 'name' => 'Fax Machine Thermal Paper', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 12.00, 'unit_price' => 22.99],
            ['sku' => 'DS-011', 'name' => 'Overhead Projector Sheet', 'category' => 'Electronics', 'supplier_id' => 3, 'unit_cost' => 20.00, 'unit_price' => 39.99],
            ['sku' => 'DS-012', 'name' => 'CD-ROM Storage Tower', 'category' => 'Home Goods', 'supplier_id' => 1, 'unit_cost' => 22.00, 'unit_price' => 44.99],
            ['sku' => 'DS-013', 'name' => 'Palm Pilot Stylus Set', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 7.00, 'unit_price' => 14.99],
            ['sku' => 'DS-014', 'name' => 'MiniDisc Player', 'category' => 'Electronics', 'supplier_id' => 1, 'unit_cost' => 75.00, 'unit_price' => 139.99],
            ['sku' => 'DS-015', 'name' => 'Zip Drive Disk Pack', 'category' => 'Electronics', 'supplier_id' => 3, 'unit_cost' => 18.00, 'unit_price' => 34.99],
            ['sku' => 'DS-016', 'name' => 'LaserDisc Cleaning Kit', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 14.00, 'unit_price' => 27.99],
            ['sku' => 'DS-017', 'name' => 'Dial-Up Modem External', 'category' => 'Electronics', 'supplier_id' => 1, 'unit_cost' => 30.00, 'unit_price' => 59.99],
            ['sku' => 'DS-018', 'name' => 'Answering Machine Tape', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 6.00, 'unit_price' => 11.99],
            ['sku' => 'DS-019', 'name' => 'Polaroid Film Pack', 'category' => 'Electronics', 'supplier_id' => 3, 'unit_cost' => 25.00, 'unit_price' => 49.99],
            ['sku' => 'DS-020', 'name' => 'Game Cartridge Case', 'category' => 'Toys', 'supplier_id' => 1, 'unit_cost' => 9.00, 'unit_price' => 17.99],
        ];

        foreach ($deadStockSkus as $product) {
            Product::create($product);
        }

        // Create 5 low-margin SKUs (misleading revenue but bad margin)
        $lowMarginSkus = [
            ['sku' => 'LM-001', 'name' => '4K TV 65 inch', 'category' => 'Electronics', 'supplier_id' => 1, 'unit_cost' => 580.00, 'unit_price' => 599.99], // 3.3% margin
            ['sku' => 'LM-002', 'name' => 'Gaming Console Pro', 'category' => 'Electronics', 'supplier_id' => 2, 'unit_cost' => 475.00, 'unit_price' => 489.99], // 3% margin
            ['sku' => 'LM-003', 'name' => 'Laptop Computer Budget', 'category' => 'Electronics', 'supplier_id' => 1, 'unit_cost' => 380.00, 'unit_price' => 399.99], // 5% margin
            ['sku' => 'LM-004', 'name' => 'Refrigerator Compact', 'category' => 'Home Goods', 'supplier_id' => 3, 'unit_cost' => 285.00, 'unit_price' => 299.99], // 5% margin
            ['sku' => 'LM-005', 'name' => 'Treadmill Home Fitness', 'category' => 'Sports', 'supplier_id' => 2, 'unit_cost' => 465.00, 'unit_price' => 479.99], // 3% margin
        ];

        foreach ($lowMarginSkus as $product) {
            Product::create($product);
        }

        // Generate remaining SKUs to reach 1,200 total
        $currentCount = count($highVelocitySkus) + count($deadStockSkus) + count($lowMarginSkus);
        $remainingCount = 1200 - $currentCount;

        $productNames = [
            'Widget', 'Gadget', 'Device', 'Tool', 'Kit', 'Set', 'Pack', 'Bundle',
            'Organizer', 'Holder', 'Stand', 'Mount', 'Adapter', 'Cable', 'Case', 'Cover',
            'Protector', 'Guard', 'Shield', 'Wrap', 'Cleaner', 'Polish', 'Spray', 'Solution',
        ];

        $adjectives = [
            'Premium', 'Deluxe', 'Standard', 'Basic', 'Pro', 'Ultra', 'Mini', 'Compact',
            'Heavy Duty', 'Light Weight', 'Portable', 'Wireless', 'Digital', 'Manual',
        ];

        for ($i = 1; $i <= $remainingCount; $i++) {
            $sku = 'SKU-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $name = $adjectives[array_rand($adjectives)] . ' ' . $productNames[array_rand($productNames)] . ' ' . $i;
            $category = $categories[array_rand($categories)];
            $supplierId = mt_rand(1, 3);

            // Random cost/price with decent margins (20-50%)
            $unitCost = mt_rand(500, 15000) / 100; // $5 to $150
            $marginMultiplier = mt_rand(120, 150) / 100; // 20-50% margin
            $unitPrice = round($unitCost * $marginMultiplier, 2);

            Product::create([
                'sku' => $sku,
                'name' => $name,
                'category' => $category,
                'supplier_id' => $supplierId,
                'unit_cost' => $unitCost,
                'unit_price' => $unitPrice,
                'pack_size' => 1,
            ]);
        }
    }
}
