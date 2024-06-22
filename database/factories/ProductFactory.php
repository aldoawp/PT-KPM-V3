<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'product_name' => fake()->word(),
            'category_id' => fake()->randomNumber(1, \App\Models\Category::count()),
            'supplier_id' => fake()->randomNumber(1, \App\Models\Supplier::count()),
            'product_garage' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'product_store' => fake()->numberBetween(100, 1000),
            'buying_price' => fake()->numberBetween(1000, 20000),
            'selling_price' => fake()->numberBetween(1000, 20000),
            'buying_date' => Carbon::now(),
            'expire_date' => Carbon::now()->addYears(2),
            'branch_id' => fake()->randomNumber(1, \App\Models\Branch::count())
        ];
    }
}
