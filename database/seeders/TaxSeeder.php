<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            [
                'name' => 'PPh Tier 1',
                'description' => 'Tax rate of 5% for annual income up to Rp60.000.000',
                'rate' => 0.05, // 5%
                'threshold' => '0-60000000',
            ],
            [
                'name' => 'PPh Tier 2',
                'description' => 'Tax rate of 15% for annual income over Rp60.000.000 up to Rp250.000.000',
                'rate' => 0.15, // 15%
                'threshold' => '60000001-250000000',
            ],
            [
                'name' => 'PPh Tier 3',
                'description' => 'Tax rate of 25% for annual income over Rp250.000.000 up to Rp500.000.000',
                'rate' => 0.25, // 25%
                'threshold' => '250000001-500000000',
            ],
            [
                'name' => 'PPh Tier 4',
                'description' => 'Tax rate of 30% for annual income over Rp500.000.000',
                'rate' => 0.30, // 30%
                'threshold' => '500000001-999999999999',
            ],
        ];

        foreach ($taxes as $tax) {
            Tax::create($tax);
        }
    }
}
