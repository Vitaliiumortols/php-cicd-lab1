<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'number' => 'ORD-'.fake()->unique()->numerify('######'),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_kop' => fake()->numberBetween(2500000, 10000000),
            'comment' => fake()->optional()->sentence(),
            'checkout_key' => (string) Str::uuid(),
            'reserve_expires_at' => now()->addMinutes(30),
            'paid_at' => null,
        ];
    }
}
