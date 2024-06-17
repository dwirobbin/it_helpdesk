<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppSetting::query()->create([
            'name' => 'My App',
            'slug' => 'my-app',
            'footer' => 'All Rights Reserved',
            'company_name' => 'Agency',
        ]);
    }
}
