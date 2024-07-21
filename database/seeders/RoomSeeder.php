<?php

namespace Database\Seeders;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::query()->create(['name' => 'R001']);
        Room::query()->create(['name' => 'R002']);
        Room::query()->create(['name' => 'R003']);
        Room::query()->create(['name' => 'R004']);
        Room::query()->create(['name' => 'R005']);
    }
}
