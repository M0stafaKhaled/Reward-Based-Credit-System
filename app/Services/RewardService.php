<?php
namespace App\Services;

use App\Repositories\RewardPointRepositoryInterface;
use App\Repositories\OfferPoolRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RewardService
{
    protected $rewardPointRepo;
    protected $offerPoolRepo;
    protected $productRepo;

    public function __construct(
        RewardPointRepositoryInterface $rewardPointRepo,
        OfferPoolRepositoryInterface $offerPoolRepo,
        ProductRepositoryInterface $productRepo
    ) {
        $this->rewardPointRepo = $rewardPointRepo;
        $this->offerPoolRepo = $offerPoolRepo;
        $this->productRepo = $productRepo;
    }

    public function getUserPoints($userId)
    {
        $earned = $this->rewardPointRepo->all()->where('user_id', $userId)->where('type', 'earn')->sum('points');
        $redeemed = $this->rewardPointRepo->all()->where('user_id', $userId)->where('type', 'redeem')->sum('points');
        return $earned - $redeemed;
    }

    public function redeemProduct($userId, $productId)
    {
        return DB::transaction(function () use ($userId, $productId) {
            $offer = $this->offerPoolRepo->all()->where('product_id', $productId)->where('is_active', true)->first();
            if (!$offer) {
                throw new \Exception('Product not in offer pool');
            }
            $product = $this->productRepo->find($productId);
            if (!$product || !$product->is_active) {
                throw new \Exception('Product not found or inactive');
            }
            $userPoints = $this->getUserPoints($userId);
            if ($userPoints < $product->price_in_points) {
                throw new \Exception('Insufficient points');
            }
            $this->rewardPointRepo->create([
                'user_id' => $userId,
                'points' => $product->price_in_points,
                'type' => 'redeem',
                'reference_id' => $product->id,
                'description' => 'Redeemed product',
            ]);
            // Log credit operation for redemption
            $user = \App\Models\User::find($userId);
            \App\Models\CreditLog::create([
                'user_id' => $userId,
                'type' => 'redeem',
                'amount' => -$product->price_in_points,
                'balance_after' => $user ? $user->credits : null,
                'description' => 'Redeemed product',
                'reference_id' => $product->id,
            ]);
            return $product;
        });
    }
} 