<?php

namespace Database\Seeders;

use App\Models\CreditPackage;
use Illuminate\Database\Seeder;

class CreditPackageSeeder extends Seeder
{
    public function run(): void
    {
        // Create standard packages
        $packages = [
            [
                'name' => 'Starter Pack',
                'credits' => 100,
                'price' => 10,
                'reward_points' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Popular Pack',
                'credits' => 500,
                'price' => 45,
                'reward_points' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Premium Pack',
                'credits' => 1000,
                'price' => 85,
                'reward_points' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Ultimate Pack',
                'credits' => 2000,
                'price' => 160,
                'reward_points' => 200,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            CreditPackage::create($package);
        }

        // Create some inactive packages for testing
        CreditPackage::factory()->count(3)->create(['is_active' => false]);
    }
} 