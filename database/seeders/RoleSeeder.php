<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => RoleEnum::SUPER_ADMIN->label()],
            ['name' => RoleEnum::IT_SUPPORT->label()],
            ['name' => RoleEnum::STAFF->label()],
        ];

        $timestamp = Carbon::now();

        foreach ($roles as &$record) {
            $record['created_at'] = $timestamp;
            $record['updated_at'] = $timestamp;
        }

        Role::query()->insert($roles);
    }
}
