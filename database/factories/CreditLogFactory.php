<?php
namespace Database\Factories;

use App\Models\CreditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditLogFactory extends Factory
{
    protected $model = CreditLog::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['purchase', 'redeem', 'admin_adjustment']),
            'amount' => $this->faker->randomFloat(2, -100, 100),
            'balance_after' => $this->faker->randomFloat(2, 0, 2000),
            'description' => $this->faker->sentence(),
            'reference_id' => null,
        ];
    }
} 