<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Global Supply Co', 'code' => 'SUP-A', 'lead_time_days' => 60, 'payment_terms_days' => 30, 'has_delays' => false],
            ['name' => 'Pacific Imports', 'code' => 'SUP-B', 'lead_time_days' => 60, 'payment_terms_days' => 30, 'has_delays' => true], // This one has delays
            ['name' => 'Domestic Goods Inc', 'code' => 'SUP-C', 'lead_time_days' => 60, 'payment_terms_days' => 30, 'has_delays' => false],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
