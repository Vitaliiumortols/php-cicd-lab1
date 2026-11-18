<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::factory()->create([
            'name' => 'Тестовий покупець',
            'email' => 'buyer@iphonehub.test',
            'password' => Hash::make('password'),
            'role' => 'buyer',
        ]);

        $tags = collect([
            ['name' => 'Акція', 'slug' => 'aktsiia'],
            ['name' => 'Новинка', 'slug' => 'novynka'],
            ['name' => 'Топ продажів', 'slug' => 'top-prodazhiv'],
            ['name' => 'Рекомендовано', 'slug' => 'rekomendovano'],
        ])->map(fn (array $tag) => Tag::create($tag));

        $categories = collect([
            ['name' => 'Базові моделі', 'slug' => 'base', 'description' => 'Базова лінійка iPhone'],
            ['name' => 'Pro моделі', 'slug' => 'pro', 'description' => 'Професійна лінійка iPhone'],
            ['name' => 'Новинки', 'slug' => 'new', 'description' => 'Нові моделі iPhone'],
        ])->map(fn (array $category) => Category::create($category));

        foreach ($categories as $category) {
            Product::factory()->count(4)->create([
                'category_id' => $category->id,
            ])->each(function (Product $product) use ($tags): void {
                $product->tags()->sync($tags->random(2)->pluck('id')->all());
            });
        }

        $firstProduct = Product::firstOrFail();
        $cart = Cart::create(['user_id' => $buyer->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $firstProduct->id, 'quantity' => 1]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'number' => 'ORD-000001',
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_kop' => $firstProduct->price_kop,
            'checkout_key' => (string) Str::uuid(),
            'reserve_expires_at' => now()->addMinutes(30),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $firstProduct->id,
            'product_name' => $firstProduct->model_name.' '.$firstProduct->storage_gb.' GB '.$firstProduct->color,
            'sku' => $firstProduct->sku,
            'quantity' => 1,
            'unit_price_kop' => $firstProduct->price_kop,
        ]);

        Shipment::create([
            'order_id' => $order->id,
            'recipient' => $buyer->name,
            'phone' => $buyer->phone ?? '+380501112233',
            'address' => 'Київ, відділення №1',
            'carrier' => 'Nova Poshta',
            'status' => 'planned',
        ]);
    }
}
