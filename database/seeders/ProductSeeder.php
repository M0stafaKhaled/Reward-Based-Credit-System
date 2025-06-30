<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Electronics category
        $electronics = [
            [
                'name' => 'iPhone 14 Pro',
                'description' => 'Latest iPhone model with advanced features',
                'category' => 'Electronics',
                'price_in_points' => 1000,
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy S23',
                'description' => 'Premium Android smartphone',
                'category' => 'Electronics',
                'price_in_points' => 900,
                'is_active' => true,
            ],
            [
                'name' => 'MacBook Air M2',
                'description' => 'Lightweight laptop with M2 chip',
                'category' => 'Electronics',
                'price_in_points' => 1500,
                'is_active' => true,
            ],
        ];

        // Fashion category
        $fashion = [
            [
                'name' => 'Nike Air Max',
                'description' => 'Comfortable running shoes',
                'category' => 'Fashion',
                'price_in_points' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Leather Wallet',
                'description' => 'Premium leather wallet',
                'category' => 'Fashion',
                'price_in_points' => 100,
                'is_active' => true,
            ],
        ];

        // Books category
        $books = [
            [
                'name' => 'The Art of Programming',
                'description' => 'Learn programming fundamentals',
                'category' => 'Books',
                'price_in_points' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Business Strategy',
                'description' => 'Guide to business strategy',
                'category' => 'Books',
                'price_in_points' => 40,
                'is_active' => true,
            ],
        ];

        // Create all defined products
        foreach (array_merge($electronics, $fashion, $books) as $product) {
            Product::create($product);
        }

        // Create additional random products
        Product::factory()->count(5)->create(['category' => 'Electronics', 'is_active' => true]);
        Product::factory()->count(5)->create(['category' => 'Fashion', 'is_active' => true]);
        Product::factory()->count(5)->create(['category' => 'Books', 'is_active' => true]);

        // Create some inactive products
        Product::factory()->count(5)->create(['is_active' => false]);
    }
} 