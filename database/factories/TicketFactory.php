<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Enums\TicketStatusEnum;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filepath = 'image/complaints';
        if (!Storage::disk('public')->directoryExists($filepath)) {
            Storage::disk('public')->makeDirectory($filepath, 0775, true);
        }

        return [
            'title' => fake()->words(5, true),
            'description' => fake()->paragraphs(5, true),
            'image' => fake()->image(Storage::disk('public')->path($filepath), 640, 680, null, false),
            'user_id' => User::where('role_id', '!=', RoleEnum::SUPER_ADMIN->value)->inRandomOrder()->value('id'),
            'status' => fake()->randomElement(TicketStatusEnum::values()),
        ];
    }
}
