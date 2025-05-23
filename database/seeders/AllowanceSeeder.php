<?php

namespace Database\Seeders;

use App\Models\Allowance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allowances = [
            [
                'name' => 'Transportation Allowance',
                'description' => 'Allowance provided for daily commuting expenses.',
                'amount' => 500000,
                'rule' => 'fixed',
                'applies_to_all' => true
            ],
            [
                'name' => 'Meal Allowance',
                'description' => 'Daily meal stipend for employees.',
                'amount' => 300000,
                'rule' => 'fixed',
                'applies_to_all' => true
            ],
            [
                'name' => 'Housing Allowance',
                'description' => 'Monthly housing support for eligible employees.',
                'amount' => 1000000,
                'rule' => 'fixed',
                'applies_to_all' => true
            ],
            [
                'name' => 'Performance Bonus',
                'description' => 'Given based on performance review, calculated as a percentage of base salary.',
                'amount' => 0.1, // 10%
                'rule' => 'percentage',
            ],
            [
                'name' => 'Overtime Allowance',
                'description' => 'Allowance provided for employees who work overtime.',
                'amount' => 50000,
                'rule' => 'fixed',
            ],
            [
                'name' => 'Hari Raya Allowance',
                'description' => 'Allowance provided for employees who celebrate Hari Raya.',
                'amount' => 0.8,
                'rule' => 'percentage',
                'applies_to_all' => true
            ]
        ];

        foreach ($allowances as $allowance) {
            Allowance::create($allowance);
        }
    }
}
