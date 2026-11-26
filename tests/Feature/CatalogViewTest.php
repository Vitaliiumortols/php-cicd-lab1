<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_displays_entities_and_actions(): void
    {
        $category = Category::factory()->create(['name' => 'Pro моделі']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'model_name' => 'Унікальний Тестовий iPhone',
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Унікальний Тестовий iPhone')
            ->assertSee('Pro моделі')
            ->assertSee('Додати товар')
            ->assertSee(route('catalog.edit', $product), false);
    }

    public function test_create_form_validates_input_and_keeps_old_values(): void
    {
        $response = $this->from(route('catalog.create'))->post(route('catalog.store'), [
            'model_name' => '',
            'sku' => '',
            'price_kop' => 'invalid',
        ]);

        $response->assertRedirect(route('catalog.create'))
            ->assertSessionHasErrors(['model_name', 'sku', 'price_kop'])
            ->assertSessionDoesntHaveErrors('category_id');
    }

    public function test_html_form_creates_product_and_syncs_tags(): void
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $response = $this->post(route('catalog.store'), [
            'category_id' => $category->id,
            'model_name' => 'iPhone 17 Pro',
            'color' => 'Titanium',
            'storage_gb' => 256,
            'sku' => 'IPH-17P-0256',
            'price_kop' => 6499900,
            'stock_qty' => 10,
            'reserved_qty' => 0,
            'active' => 1,
            'tags' => $tags->pluck('id')->all(),
        ]);

        $product = Product::where('sku', 'IPH-17P-0256')->firstOrFail();
        $response->assertRedirect(route('catalog.index'));
        $this->assertEqualsCanonicalizing($tags->pluck('id')->all(), $product->tags()->pluck('tags.id')->all());
    }

    public function test_edit_uses_put_and_delete_removes_product(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->put(route('catalog.update', $product), [
            'category_id' => $category->id,
            'model_name' => 'Оновлений iPhone',
            'color' => $product->color,
            'storage_gb' => $product->storage_gb,
            'sku' => $product->sku,
            'price_kop' => $product->price_kop,
            'stock_qty' => $product->stock_qty,
            'reserved_qty' => 0,
            'active' => 1,
            'tags' => [],
        ])->assertRedirect(route('catalog.show', $product));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'model_name' => 'Оновлений iPhone']);

        $this->delete(route('catalog.destroy', $product))->assertRedirect(route('catalog.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_forms_render_csrf_tokens_and_method_spoofing_fields(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->get(route('catalog.create'))
            ->assertOk()
            ->assertSee('name="_token"', false);

        $this->get(route('catalog.edit', $product))
            ->assertOk()
            ->assertSee('name="_token"', false)
            ->assertSee('name="_method" value="PUT"', false);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('name="_method" value="DELETE"', false);
    }

    public function test_catalog_filters_by_projected_product_attributes(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create([
            'category_id' => $category->id,
            'model_name' => 'iPhone Filter Match',
            'color' => 'Blue',
            'storage_gb' => 256,
            'price_kop' => 4500000,
            'active' => true,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'model_name' => 'iPhone Filter Miss',
            'color' => 'Black',
            'storage_gb' => 128,
            'price_kop' => 3000000,
            'active' => false,
        ]);

        $this->get(route('catalog.index', [
            'category' => $category->id,
            'storage_gb' => 256,
            'color' => 'Blue',
            'price_from' => 40000,
            'price_to' => 50000,
            'active' => 1,
        ]))
            ->assertOk()
            ->assertSee('iPhone Filter Match')
            ->assertDontSee('iPhone Filter Miss');
    }
}
