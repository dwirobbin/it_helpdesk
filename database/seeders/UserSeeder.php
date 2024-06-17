<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'role_id' => RoleEnum::SUPER_ADMIN->value,
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Xonny',
            'email' => 'xonny@gmail.com',
            'role_id' => RoleEnum::IT_SUPPORT->value,
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'xanny',
            'email' => 'xanny@gmail.com',
            'role_id' => RoleEnum::STAFF->value,
            'is_active' => true,
        ]);

        // User::factory(13)->create();
    }
}
