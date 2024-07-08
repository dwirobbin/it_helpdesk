<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $imageFilepath = 'image';
        if (Storage::disk('public')->directoryExists($imageFilepath)) {
            Storage::disk('public')->deleteDirectory($imageFilepath); // delete all files
        }

        $avatarFilepath = $imageFilepath . '/avatars';
        if (Storage::disk('public')->directoryExists($avatarFilepath)) {
            Storage::disk('public')->deleteDirectory($avatarFilepath); // delete all files
        }

        $complaintFilepath = $imageFilepath . '/complaints';
        if (Storage::disk('public')->directoryExists($complaintFilepath)) {
            Storage::disk('public')->deleteDirectory($complaintFilepath); // delete all files
        }

        $respondFilepath = $imageFilepath . '/responds';
        if (Storage::disk('public')->directoryExists($respondFilepath)) {
            Storage::disk('public')->deleteDirectory($respondFilepath); // delete all files
        }

        $this->call([
            SettingSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            // PositionSeeder::class,
            // DepartmentSeeder::class,
            EmployeeSeeder::class,
            // TicketSeeder::class,
        ]);
    }
}
