<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_products_with_relations(): void
    {
        $category = Category::factory()->create(['name' => 'Смартфони']);
        $tag = Tag::factory()->create(['name' => 'Акція']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'model_name' => 'iPhone 16',
            'active' => true,
        ]);
        $product->tags()->attach($tag);

        $this->getJson('/catalog')
            ->assertOk()
            ->assertJsonFragment(['model_name' => 'iPhone 16'])
            ->assertJsonFragment(['name' => 'Смартфони'])
            ->assertJsonFragment(['name' => 'Акція']);
    }

    public function test_can_create_product_with_category_and_tags(): void
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $response = $this->postJson('/catalog', [
            'category_id' => $category->id,
            'model_name' => 'iPhone 16 Pro',
            'color' => 'Titanium',
            'storage_gb' => 256,
            'sku' => 'IPH-16P-0256',
            'price_kop' => 5499900,
            'stock_qty' => 12,
            'reserved_qty' => 0,
            'active' => true,
            'tags' => $tags->pluck('id')->all(),
        ])->assertCreated();

        $productId = $response->json('data.id');
        $this->assertDatabaseHas('products', ['id' => $productId, 'sku' => 'IPH-16P-0256']);

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('product_tag', [
                'product_id' => $productId,
                'tag_id' => $tag->id,
            ]);
        }
    }

    public function test_update_uses_sync_for_tags(): void
    {
        $category = Category::factory()->create();
        $oldTag = Tag::factory()->create();
        $newTag = Tag::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $product->tags()->attach($oldTag);

        $this->putJson('/catalog/'.$product->id, [
            'category_id' => $category->id,
            'model_name' => $product->model_name,
            'color' => $product->color,
            'storage_gb' => $product->storage_gb,
            'sku' => $product->sku,
            'price_kop' => $product->price_kop,
            'stock_qty' => $product->stock_qty,
            'reserved_qty' => 0,
            'active' => true,
            'tags' => [$newTag->id],
        ])->assertOk();

        $this->assertDatabaseMissing('product_tag', ['product_id' => $product->id, 'tag_id' => $oldTag->id]);
        $this->assertDatabaseHas('product_tag', ['product_id' => $product->id, 'tag_id' => $newTag->id]);
    }
}
