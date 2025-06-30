<?php
namespace Database\Factories;

use App\Models\CreditPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditPackageFactory extends Factory
{
    protected $model = CreditPackage::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'credits' => $this->faker->numberBetween(10, 1000),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'reward_points' => $this->faker->numberBetween(10, 1000),
            'is_active' => true,
        ];
    }
} 