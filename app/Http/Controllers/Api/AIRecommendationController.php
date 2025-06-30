<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIRecommendationService;
use App\Services\RewardService;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;

class AIRecommendationController extends Controller
{
    protected $aiService;
    protected $rewardService;
    protected $productRepo;

    public function __construct(
        AIRecommendationService $aiService,
        RewardService $rewardService,
        ProductRepositoryInterface $productRepo
    ) {
        $this->aiService = $aiService;
        $this->rewardService = $rewardService;
        $this->productRepo = $productRepo;
    }

    public function recommend(Request $request)
    {
        $userId = $request->user()->id;
        $userPoints = $this->rewardService->getUserPoints($userId);
        $products = $this->productRepo->all();
        $recommended = $this->aiService->recommendProduct($userPoints, $products);
        return response()->json(['recommended' => $recommended]);
    }
} 