<?php
namespace Database\Factories;

use App\Models\RewardPoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RewardPointFactory extends Factory
{
    protected $model = RewardPoint::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'points' => $this->faker->numberBetween(10, 1000),
            'type' => $this->faker->randomElement(['earn', 'redeem']),
            'reference_id' => null,
            'description' => $this->faker->sentence(),
        ];
    }
} 