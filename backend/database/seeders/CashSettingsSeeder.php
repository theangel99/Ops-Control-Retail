<?php

namespace Database\Seeders;

use App\Models\CashSettings;
use Illuminate\Database\Seeder;

class CashSettingsSeeder extends Seeder
{
    public function run(): void
    {
        CashSettings::create([
            'starting_cash' => 500000.00,
            'revenue_collection_delay_days' => 14,
            'payment_terms_days' => 30,
        ]);
    }
}
