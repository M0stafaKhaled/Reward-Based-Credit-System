<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\CreditPackage;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::where('is_active', true)->get();
        $creditPackages = CreditPackage::where('is_active', true)->get();

        foreach ($users as $user) {
            // Create credit package purchases
            for ($i = 0; $i < rand(2, 5); $i++) {
                $package = $creditPackages->random();
                Purchase::create([
                    'user_id' => $user->id,
                    'credit_package_id' => $package->id,
                    'credits' => $package->credits,
                    'price' => $package->price,
                    'reward_points' => $package->reward_points,
                    'status' => 'complete',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
} 