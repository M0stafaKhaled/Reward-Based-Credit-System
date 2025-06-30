<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RewardService;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use App\Http\Requests\RedeemRequest;
use Illuminate\Support\Facades\Cache;
use App\Models\IdempotencyKey;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Log;

class RewardController extends Controller
{
    protected $rewardService;
    protected $responseService;

    public function __construct(
        RewardService $rewardService,
        ApiResponseService $responseService
    ) {
        $this->rewardService = $rewardService;
        $this->responseService = $responseService;
    }

    public function points(Request $request)
    {
        $userId = $request->user()->id;
        $points = $this->rewardService->getUserPoints($userId);
        return response()->json(['points' => $points]);
    }

    public function redeem(RedeemRequest $request)
    {
        $userId = $request->user()->id;
        try {
            $product = $this->rewardService->redeemProduct($userId, $request->product_id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Redeem failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // Format the product data with all necessary fields
        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category,
            'price_in_points' => $product->price_in_points,
            'is_active' => (bool)$product->is_active,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];

        // Build the exact response structure expected by the test
        $response = [
            'success' => true,
            'product' => [
                'data' => $productData
            ]
        ];

        return response()->json($response, 200);
    }
}
