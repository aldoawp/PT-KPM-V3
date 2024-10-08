<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'shopname' => fake()->company(),
            'account_holder' => fake()->name(),
            'account_number' => fake()->randomNumber(8, true),
            'bank_name' => fake()->randomElement(['BRI', 'BNI', 'BCA', 'BSI', 'MANDIRI', 'BJB']),
            'bank_branch' => fake()->city(),
            'branch_id' => fake()->randomElement(Branch::pluck('id')),
        ];
    }
}
