<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_name' => 'iPhone 15 128 GB Black',
            'sku' => fake()->unique()->regexify('IPH-[A-Z]{3}-[0-9]{4}'),
            'quantity' => 1,
            'unit_price_kop' => fake()->numberBetween(2500000, 7500000),
        ];
    }
}
