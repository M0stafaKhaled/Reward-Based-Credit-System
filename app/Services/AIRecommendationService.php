<?php
namespace App\Services;

class AIRecommendationService
{
    public function recommendProduct($userPoints, $products)
    {
        // Mock logic:
        $recommended = null;
        foreach ($products as $product) {
            if ($product->price_in_points <= $userPoints) {
                if (!$recommended || $product->price_in_points > $recommended->price_in_points) {
                    $recommended = $product;
                }
            }
        }
        return $recommended;
    }
} 