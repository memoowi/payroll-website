<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin Misbach',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'password' => bcrypt('12345678'),
        ]);

        CompanySetting::factory()->create([
            'name' => 'PT. Memoowi',
            'description' => 'Perusahaan yang bergerak di bidang teknologi informasi',
            'address' => 'Jl. Raya No. 1, Jakarta',
            'phone' => '088232220652',
            'value' => 'Bersama Membangun Indonesia',
        ]);

        // $this->call([
        //     DepartmentPositionSeeder::class,
        //     AllowanceSeeder::class,
        // ]);
    }
}
