<?php

namespace Database\Factories;

use App\Models\Branch;
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
            'user_id' => fake()->randomElement(User::pluck('id')),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'address' => fake()->address(),
            'experience' => fake()->randomElement(['0 Year', '1 Year', '2 Year', '3 Year', '4 Year', '5 Year']),
            'salary' => fake()->numberBetween(1000000, 10000000),
            'vacation' => 12,
            'branch_id' => fake()->randomElement(Branch::pluck('id'))
        ];
    }
}
