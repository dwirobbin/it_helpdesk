<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::query()->create([
            'app_name' => 'App',
            'app_footer' => 'All Rights Reserved',
            'company_name' => 'Agency',
        ]);
    }
}
