<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'model_name' => fake()->randomElement(['iPhone 13', 'iPhone 14', 'iPhone 15', 'iPhone 16', 'iPhone 16 Pro']),
            'color' => fake()->randomElement(['Black', 'White', 'Blue', 'Pink', 'Titanium']),
            'storage_gb' => fake()->randomElement([128, 256, 512]),
            'sku' => fake()->unique()->regexify('IPH-[A-Z]{3}-[0-9]{4}'),
            'price_kop' => fake()->numberBetween(2500000, 7500000),
            'stock_qty' => fake()->numberBetween(5, 50),
            'reserved_qty' => 0,
            'active' => true,
        ];
    }
}
