<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_one_cart_and_many_orders(): void
    {
        $user = User::factory()->create();
        Cart::factory()->create(['user_id' => $user->id]);
        Order::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertSame(1, $user->cart()->count());
        $this->assertSame(2, $user->orders()->count());
    }

    public function test_order_has_only_one_shipment(): void
    {
        $order = Order::factory()->create();
        Shipment::factory()->create(['order_id' => $order->id]);

        $this->assertNotNull($order->shipment);
        $this->assertSame($order->id, $order->shipment->order_id);
    }

    public function test_order_route_creates_snapshot_and_reserves_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock_qty' => 10,
            'reserved_qty' => 0,
            'price_kop' => 3000000,
        ]);

        $response = $this->postJson('/orders', [
            'user_id' => $user->id,
            'checkout_key' => (string) Str::uuid(),
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ])->assertCreated();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $response->json('id'),
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price_kop' => 3000000,
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'reserved_qty' => 2]);
    }
}
