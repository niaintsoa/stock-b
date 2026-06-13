<?php

namespace Database\Factories\Domain\Entity;

use App\Domain\Entity\StockMovement;
use App\Domain\Entity\Product;
use App\Domain\Entity\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 100),
            'type' => fake()->randomElement(['entry', 'exit']),
            'reason' => fake()->sentence(),
            'status' => 'completed',
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
