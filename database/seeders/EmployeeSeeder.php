<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::query()->create([
            'user_id' => 2,
            'nik' => fake('id_ID')->nik(),
        ]);

        Employee::query()->create([
            'user_id' => 3,
            'nik' => fake('id_ID')->nik(),
        ]);

        // Employee::factory(13)->create();
    }
}
