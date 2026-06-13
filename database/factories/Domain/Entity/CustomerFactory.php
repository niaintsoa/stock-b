<?php

namespace Database\Factories\Domain\Entity;

use App\Domain\Entity\Customer;
use App\Domain\Entity\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
