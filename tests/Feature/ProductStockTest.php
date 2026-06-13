<?php

namespace Tests\Feature;

use App\Domain\Entity\Product;
use App\Domain\Entity\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_current_stock_correctly(): void
    {
        $product = Product::factory()->create();

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 50,
        ]);

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 20,
        ]);

        $this->assertEquals(30, $product->current_stock);
    }
}
