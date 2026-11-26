<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResourceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_crud_works_and_deleting_category_keeps_product(): void
    {
        $this->postJson(route('categories.store'), [
            'name' => 'Аксесуари',
            'slug' => 'accessories',
            'description' => 'Категорія для тесту',
            'active' => true,
        ])->assertCreated();

        $category = Category::where('slug', 'accessories')->firstOrFail();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->putJson(route('categories.update', $category), [
            'name' => 'Смартфони',
            'slug' => 'smartphones',
            'description' => 'Оновлений опис',
            'active' => false,
        ])->assertOk();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Смартфони',
            'active' => false,
        ]);

        $this->deleteJson(route('categories.destroy', $category))->assertOk();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'category_id' => null]);
    }

    public function test_tag_crud_works_and_pivot_rows_are_cleaned_up(): void
    {
        $this->postJson(route('tags.store'), [
            'name' => 'Новинка',
            'slug' => 'new',
        ])->assertCreated();

        $tag = Tag::where('slug', 'new')->firstOrFail();
        $product = Product::factory()->create();
        $product->tags()->attach($tag);

        $this->putJson(route('tags.update', $tag), [
            'name' => 'Хіт продажів',
            'slug' => 'bestseller',
        ])->assertOk();

        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'slug' => 'bestseller']);
        $this->deleteJson(route('tags.destroy', $tag))->assertOk();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        $this->assertDatabaseMissing('product_tag', ['tag_id' => $tag->id]);
    }

    public function test_user_crud_hashes_password_and_preserves_users_with_orders(): void
    {
        $this->postJson(route('users.store'), [
            'name' => 'Тестовий покупець',
            'email' => 'buyer@example.test',
            'password' => 'secret123',
            'phone' => '+380991112233',
            'role' => 'buyer',
            'active' => true,
        ])->assertCreated();

        $user = User::where('email', 'buyer@example.test')->firstOrFail();
        $oldPassword = $user->password;

        $this->putJson(route('users.update', $user), [
            'name' => 'Оновлений покупець',
            'email' => 'buyer@example.test',
            'password' => '',
            'phone' => '+380991112244',
            'role' => 'manager',
            'active' => true,
        ])->assertOk();

        $this->assertSame($oldPassword, $user->fresh()->password);
        Order::factory()->create(['user_id' => $user->id]);
        $this->deleteJson(route('users.destroy', $user))
            ->assertOk()
            ->assertJsonPath('status', 'archived');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'active' => false]);

        $plainUser = User::factory()->create();
        $this->deleteJson(route('users.destroy', $plainUser))->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $plainUser->id]);
    }

    public function test_cart_crud_and_item_management_work(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_qty' => 5, 'reserved_qty' => 0]);

        $response = $this->postJson(route('carts.store'), [
            'user_id' => $user->id,
            'status' => 'active',
        ])->assertCreated();

        $cart = Cart::findOrFail($response->json('id'));
        $itemResponse = $this->postJson(route('carts.items.store', $cart), [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertCreated();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->putJson(route('carts.update', $cart), [
            'user_id' => $user->id,
            'status' => 'abandoned',
        ])->assertOk();

        $this->deleteJson(route('carts.items.destroy', [$cart, $itemResponse->json('id')]))->assertOk();
        $this->assertDatabaseMissing('cart_items', ['id' => $itemResponse->json('id')]);

        $this->deleteJson(route('carts.destroy', $cart))->assertOk();
        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }

    public function test_order_cancellation_releases_reservation_once_and_keeps_history(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock_qty' => 10,
            'reserved_qty' => 0,
            'price_kop' => 4200000,
        ]);

        $response = $this->postJson(route('orders.store'), [
            'user_id' => $user->id,
            'checkout_key' => (string) Str::uuid(),
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ])->assertCreated();

        $order = Order::findOrFail($response->json('id'));
        $this->assertSame(2, $product->fresh()->reserved_qty);

        $payload = [
            'user_id' => $user->id,
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ];

        $this->putJson(route('orders.update', $order), $payload)->assertOk();
        $this->assertSame(0, $product->fresh()->reserved_qty);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->putJson(route('orders.update', $order), $payload)->assertOk();
        $this->assertSame(0, $product->fresh()->reserved_qty);

        $this->deleteJson(route('orders.destroy', $order))
            ->assertOk()
            ->assertJsonPath('status', 'cancelled');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id]);
    }

    public function test_product_with_order_history_is_archived_and_reserved_product_is_protected(): void
    {
        $product = Product::factory()->create(['active' => true, 'reserved_qty' => 0]);
        $order = Order::factory()->create();
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->model_name,
            'sku' => $product->sku,
            'quantity' => 1,
            'unit_price_kop' => $product->price_kop,
        ]);

        $this->deleteJson(route('catalog.destroy', $product))
            ->assertOk()
            ->assertJsonPath('status', 'archived');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'active' => false]);

        $reservedProduct = Product::factory()->create(['stock_qty' => 5, 'reserved_qty' => 1]);
        $this->deleteJson(route('catalog.destroy', $reservedProduct))->assertUnprocessable();
        $this->assertDatabaseHas('products', ['id' => $reservedProduct->id, 'active' => true]);
    }

    public function test_shipment_crud_works_for_one_to_one_order_relation(): void
    {
        $order = Order::factory()->create();

        $response = $this->postJson(route('shipments.store'), [
            'order_id' => $order->id,
            'recipient' => 'Віталій Томенко',
            'phone' => '+380991234567',
            'address' => 'Київ, відділення 1',
            'carrier' => 'Nova Poshta',
            'tracking_number' => '20450000000001',
            'status' => 'planned',
        ])->assertCreated();

        $shipment = Shipment::findOrFail($response->json('id'));
        $this->putJson(route('shipments.update', $shipment), [
            'recipient' => 'Віталій Томенко',
            'phone' => '+380991234567',
            'address' => 'Львів, відділення 2',
            'carrier' => 'Nova Poshta',
            'tracking_number' => '20450000000001',
            'status' => 'delivered',
            'delivered_at' => now()->toDateTimeString(),
        ])->assertOk();

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'delivered',
            'address' => 'Львів, відділення 2',
        ]);

        $this->deleteJson(route('shipments.destroy', $shipment))->assertOk();
        $this->assertDatabaseMissing('shipments', ['id' => $shipment->id]);
    }
}
