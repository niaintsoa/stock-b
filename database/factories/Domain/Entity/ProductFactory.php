<?php

namespace Database\Factories\Domain\Entity;

use App\Domain\Entity\Product;
use App\Domain\Entity\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'status' => fake()->randomElement(['active', 'inactive']),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
