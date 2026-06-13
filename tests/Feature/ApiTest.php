<?php

namespace Tests\Feature;

use App\Domain\Entity\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_platform_exposes_products(): void
    {
        Product::factory(3)->create();

        $response = $this->getJson('/api/products');
        
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_swagger_ui_is_accessible(): void
    {
        $response = $this->get('/api/docs');

        $response->assertStatus(200);
    }
}
