<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\OfferPool;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OfferPoolSeeder extends Seeder
{
    public function run(): void
    {
        // Get all active products
        $products = Product::where('is_active', true)->get();

        foreach ($products as $product) {
            // Create current offers
            OfferPool::create([
                'product_id' => $product->id,
                'is_active' => true,
                'offer_start' => Carbon::now(),
                'offer_end' => Carbon::now()->addDays(30),
            ]);

            // Randomly create future offers for some products
            if (rand(0, 1)) {
                OfferPool::create([
                    'product_id' => $product->id,
                    'is_active' => true,
                    'offer_start' => Carbon::now()->addDays(31),
                    'offer_end' => Carbon::now()->addDays(60),
                ]);
            }
        }

        // Create some inactive offers
        $randomProducts = Product::inRandomOrder()->limit(5)->get();
        foreach ($randomProducts as $product) {
            OfferPool::create([
                'product_id' => $product->id,
                'is_active' => false,
                'offer_start' => Carbon::now()->subDays(30),
                'offer_end' => Carbon::now()->subDays(1),
            ]);
        }
    }
} 