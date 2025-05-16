<?php

namespace Database\Seeders;

use App\Models\Deduction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $deductions = [
            [
                'name' => 'Late Arrival',
                'description' => 'Deduction for arriving late to work.',
                'amount' => 50000,
            ],
            [
                'name' => 'Absence Without Notice',
                'description' => 'Deduction for being absent without permission.',
                'amount' => 100000,
            ],
            [
                'name' => 'Damage to Company Property',
                'description' => 'Deduction for damaging office equipment or property.',
                'amount' => 250000,
            ],
            [
                'name' => 'Loan Repayment',
                'description' => 'Monthly repayment deduction for company loans.',
                'amount' => 150000,
            ],
            [
                'name' => 'Social Security Contribution',
                'description' => 'Mandatory employee contribution to BPJS Ketenagakerjaan.',
                'amount' => 100000,
            ],
        ];

        foreach ($deductions as $deduction) {
            Deduction::create($deduction);
        }
    }
}
