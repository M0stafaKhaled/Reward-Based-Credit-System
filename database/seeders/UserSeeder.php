<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        User::create([
            'name' => 'Demo User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'credits' => 1000,
        ]);

        // Create users with varying credit amounts
        User::factory()->count(5)->create(['credits' => 500]);
        User::factory()->count(5)->create(['credits' => 1000]);
        User::factory()->count(5)->create(['credits' => 2000]);
    }
} 