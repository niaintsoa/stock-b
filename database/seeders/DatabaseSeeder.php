<?php

namespace Database\Seeders;

use App\Domain\Entity\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'test@example.com',
        ]);

        $users = User::factory(5)->create();

        $customers = \App\Domain\Entity\Customer::factory(20)->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $products = \App\Domain\Entity\Product::factory(10)->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        foreach ($products as $product) {
            \App\Domain\Entity\StockMovement::factory(rand(2, 5))->create([
                'product_id' => $product->id,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'type' => 'entry',
            ]);
        }
    }
}
