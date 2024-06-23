<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'category_id' => fake()->randomElement(Category::pluck('id')),
            'supplier_id' => fake()->randomElement(Supplier::pluck('id')),
            'product_garage' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'product_store' => fake()->numberBetween(100, 1000),
            'buying_price' => fake()->numberBetween(1000, 20000),
            'selling_price' => fake()->numberBetween(1000, 20000),
            'buying_date' => Carbon::now(),
            'expire_date' => Carbon::now()->addYears(2),
            'branch_id' => fake()->randomElement(Branch::pluck('id')),
        ];
    }
}
