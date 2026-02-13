<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Central Warehouse', 'code' => 'WH-01', 'is_warehouse' => true, 'city' => 'Chicago', 'state' => 'IL'],
            ['name' => 'Store - New York', 'code' => 'ST-NY', 'is_warehouse' => false, 'city' => 'New York', 'state' => 'NY'],
            ['name' => 'Store - Los Angeles', 'code' => 'ST-LA', 'is_warehouse' => false, 'city' => 'Los Angeles', 'state' => 'CA'],
            ['name' => 'Store - Chicago', 'code' => 'ST-CH', 'is_warehouse' => false, 'city' => 'Chicago', 'state' => 'IL'],
            ['name' => 'Store - Houston', 'code' => 'ST-HO', 'is_warehouse' => false, 'city' => 'Houston', 'state' => 'TX'],
            ['name' => 'Store - Phoenix', 'code' => 'ST-PH', 'is_warehouse' => false, 'city' => 'Phoenix', 'state' => 'AZ'],
            ['name' => 'Store - Philadelphia', 'code' => 'ST-PD', 'is_warehouse' => false, 'city' => 'Philadelphia', 'state' => 'PA'],
            ['name' => 'Store - San Antonio', 'code' => 'ST-SA', 'is_warehouse' => false, 'city' => 'San Antonio', 'state' => 'TX'],
            ['name' => 'Store - San Diego', 'code' => 'ST-SD', 'is_warehouse' => false, 'city' => 'San Diego', 'state' => 'CA'],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
