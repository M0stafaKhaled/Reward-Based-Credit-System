<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create admins first
        $this->call([
            AdminSeeder::class,
        ]);

        // Create users
        $this->call([
            UserSeeder::class,
        ]);

        // Create products and credit packages
        $this->call([
            CreditPackageSeeder::class,
            ProductSeeder::class,
        ]);

        // Create offers and rewards
        $this->call([
            OfferPoolSeeder::class,
            RewardPointSeeder::class,
        ]);

        // Create purchases last as they depend on other models
        $this->call([
            PurchaseSeeder::class,
        ]);
    }
}
