<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'recipient' => fake()->name(),
            'phone' => fake()->numerify('+380#########'),
            'address' => fake()->city().', відділення №'.fake()->numberBetween(1, 50),
            'carrier' => 'Nova Poshta',
            'tracking_number' => fake()->unique()->numerify('2045##########'),
            'status' => 'planned',
            'delivered_at' => null,
        ];
    }
}
