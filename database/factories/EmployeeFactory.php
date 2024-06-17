<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->unique()->randomElement(User::where('id', '!=', 1)->pluck('id')->toArray()),
            'position_id' => Position::inRandomOrder()->value('id'),
            'department_id' => Department::inRandomOrder()->value('id'),
            'nik' => fake('id_ID')->nik(),
        ];
    }
}
