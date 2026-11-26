<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_list_is_available_as_json(): void
    {
        Product::factory()->count(3)->create();

        $this->getJson(route('catalog.index'))
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(3, 'data');
    }

    public function test_one_database_product_can_be_found(): void
    {
        $product = Product::factory()->create(['model_name' => 'iPhone 17']);

        $this->getJson(route('catalog.show', $product))
            ->assertOk()
            ->assertJsonPath('data.model_name', 'iPhone 17');
    }

    public function test_unknown_product_returns_404(): void
    {
        $this->getJson('/catalog/999')->assertNotFound();
    }

    public function test_json_request_creates_real_database_product(): void
    {
        $category = Category::factory()->create();

        $this->postJson(route('catalog.store'), $this->newProductData($category))
            ->assertCreated()
            ->assertJsonPath('status', 'created')
            ->assertJsonPath('data.model_name', 'iPhone 17');

        $this->assertDatabaseHas('products', ['sku' => 'IPH-17-BLUE-256']);
    }

    public function test_category_is_optional_as_designed_in_lab_two(): void
    {
        $data = $this->newProductData();
        $data['sku'] = 'IPH-17-NOCATEGORY';

        $this->postJson(route('catalog.store'), $data)->assertCreated();

        $this->assertDatabaseHas('products', [
            'sku' => 'IPH-17-NOCATEGORY',
            'category_id' => null,
        ]);
    }

    public function test_validation_errors_are_returned(): void
    {
        $this->postJson(route('catalog.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'model_name',
                'color',
                'storage_gb',
                'sku',
                'price_kop',
                'stock_qty',
            ]);
    }

    private function newProductData(?Category $category = null): array
    {
        return [
            'category_id' => $category?->id,
            'model_name' => 'iPhone 17',
            'color' => 'Blue',
            'storage_gb' => 256,
            'sku' => 'IPH-17-BLUE-256',
            'price_kop' => 4999900,
            'stock_qty' => 10,
            'reserved_qty' => 0,
            'active' => true,
            'tags' => [],
        ];
    }
}
