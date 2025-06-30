<?php
namespace Database\Factories;

use App\Models\OfferPool;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferPoolFactory extends Factory
{
    protected $model = OfferPool::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'is_active' => true,
            'offer_start' => null,
            'offer_end' => null,
        ];
    }
} 