<?php

namespace Database\Seeders;

use App\Domain\Entity\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminProfile = \App\Domain\Entity\Admin::create(['role' => 'super_admin']);
        $admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'test@example.com',
            'profile_id' => $adminProfile->id,
            'profile_type' => \App\Domain\Entity\Admin::class,
        ]);

        $customers = \App\Domain\Entity\Customer::factory(20)->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        foreach ($customers as $customer) {
            User::factory()->create([
                'name' => $customer->first_name . ' ' . $customer->last_name,
                'email' => fake()->unique()->safeEmail(),
                'profile_id' => $customer->id,
                'profile_type' => \App\Domain\Entity\Customer::class,
            ]);
        }

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
