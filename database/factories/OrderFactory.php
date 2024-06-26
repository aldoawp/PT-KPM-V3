<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => fake()->randomElement(Customer::pluck('id')),
            'order_status' => fake()->randomElement(['complete', 'pending']),
            'total_products' => fake()->numberBetween(1, 10),
            'sub_total' => fake()->numberBetween(1000, 10000),
            'vat' => 0,
            'invoice_no' => 'INV-' . fake()->numberBetween(1, 100),
            'total' => fake()->numberBetween(1000, 10000),
            'payment_status' => fake()->randomElement(['tunai', 'cek', 'bon']),
            'branch_id' => fake()->randomElement(Branch::pluck('id')),
            'pay' => fake()->numberBetween(10000000, 100000000),
            'due' => fake()->numberBetween(100000, 10000000),
            'user_id' => fake()->randomElement(User::pluck('id')),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
