<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Test admin user
        User::factory()->admin()->create([
            'name' => 'Admin',
            'last_name' => 'User',
            'phone' => '+998901234567',
        ]);

        // Test regular user
        User::factory()->verified()->create([
            'name' => 'Test',
            'last_name' => 'User',
            'phone' => '+998987654321',
        ]);

        // Create some test products
        Product::factory()->count(5)->create();
    }
}
