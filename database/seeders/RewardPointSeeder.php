<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RewardPoint;
use Illuminate\Database\Seeder;

class RewardPointSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $types = ['earn', 'redeem'];

        foreach ($users as $user) {
            // Create some earning history
            for ($i = 0; $i < rand(3, 8); $i++) {
                RewardPoint::create([
                    'user_id' => $user->id,
                    'points' => rand(10, 100),
                    'type' => 'earn',
                    'description' => 'Earned from credit package purchase',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            // Create some redemption history
            for ($i = 0; $i < rand(1, 4); $i++) {
                RewardPoint::create([
                    'user_id' => $user->id,
                    'points' => -1 * rand(10, 50),
                    'type' => 'redeem',
                    'description' => 'Redeemed for product purchase',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
} 