<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InterfacePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_crud_pages_render_without_errors(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $product->tags()->attach($tag);
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id]);
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->model_name,
            'sku' => $product->sku,
            'unit_price_kop' => $product->price_kop,
        ]);
        $shipment = Shipment::factory()->create(['order_id' => $order->id]);

        $urls = [
            route('home'),
            route('catalog.index'), route('catalog.create'), route('catalog.show', $product), route('catalog.edit', $product),
            route('categories.index'), route('categories.create'), route('categories.show', $category), route('categories.edit', $category),
            route('tags.index'), route('tags.create'), route('tags.show', $tag), route('tags.edit', $tag),
            route('users.index'), route('users.create'), route('users.show', $user), route('users.edit', $user),
            route('carts.index'), route('carts.create'), route('carts.show', $cart), route('carts.edit', $cart),
            route('orders.index'), route('orders.create'), route('orders.show', $order), route('orders.edit', $order),
            route('shipments.index'), route('shipments.create'), route('shipments.show', $shipment), route('shipments.edit', $shipment),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }
}
