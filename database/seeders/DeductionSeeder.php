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
                'name' => 'Social Security Contribution',
                'description' => 'Mandatory employee contribution to BPJS Ketenagakerjaan.',
                'amount' => 100000,
                'applies_to_all' => true,
            ],
            [
                'name' => 'Violation of Dress Code',
                'description' => 'Deduction for not following office dress code policies.',
                'amount' => 25000,
            ],
            [
                'name' => 'Company Event No-show',
                'description' => 'Deduction for not attending a mandatory company event.',
                'amount' => 50000,
            ],
            [
                'name' => 'Office Supplies Misuse',
                'description' => 'Deduction for misusing or losing office supplies.',
                'amount' => 40000,
            ],
            [
                'name' => 'Unapproved Leave',
                'description' => 'Deduction for taking leave without prior approval.',
                'amount' => 60000,
            ],
            [
                'name' => 'Poor Performance',
                'description' => 'Deduction for not meeting performance targets.',
                'amount' => 150000,
            ],
        ];

        foreach ($deductions as $deduction) {
            Deduction::create($deduction);
        }
    }
}
