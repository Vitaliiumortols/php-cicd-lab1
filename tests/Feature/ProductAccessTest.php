<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ProductAccessTest extends TestCase
{
    public function test_product_list_is_available(): void
    {
        $response = $this->getJson('/products');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(3, 'data');
    }

    public function test_one_product_can_be_found(): void
    {
        $response = $this->getJson('/products/1');

        $response->assertOk()
            ->assertJsonPath('data.model_name', 'iPhone 15');
    }

    public function test_unknown_product_returns_404(): void
    {
        $response = $this->getJson('/products/999');

        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
    }

    public function test_post_without_header_is_forbidden(): void
    {
        $response = $this->postJson('/products', $this->newProductData());

        $response->assertForbidden()
            ->assertJsonPath('status', 'access_denied');
    }

    public function test_post_with_header_creates_product(): void
    {
        $response = $this->withHeader('X-Store-Client', 'StudentClient')
            ->postJson('/products', $this->newProductData());

        $response->assertCreated()
            ->assertHeader('X-Security-Policy', 'Enforced')
            ->assertJsonPath('status', 'created')
            ->assertJsonPath('data.model_name', 'iPhone 17');
    }

    public function test_validation_errors_are_returned(): void
    {
        $response = $this->withHeader('X-Store-Client', 'StudentClient')
            ->postJson('/products', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'model_name',
                'color',
                'storage_gb',
                'price',
            ]);
    }

    private function newProductData(): array
    {
        return [
            'model_name' => 'iPhone 17',
            'color' => 'Blue',
            'storage_gb' => 256,
            'price' => 49999,
        ];
    }
}
