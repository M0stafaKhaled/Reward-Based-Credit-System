<?php
namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'category' => $this->faker->word(),
            'price_in_points' => $this->faker->numberBetween(10, 1000),
            'is_active' => true,
        ];
    }
} 