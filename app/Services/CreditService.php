<?php
namespace App\Services;

use App\Repositories\CreditPackageRepositoryInterface;
use App\Repositories\PurchaseRepositoryInterface;
use App\Repositories\RewardPointRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreditService
{
    protected $creditPackageRepo;
    protected $purchaseRepo;
    protected $rewardPointRepo;

    public function __construct(
        CreditPackageRepositoryInterface $creditPackageRepo,
        PurchaseRepositoryInterface $purchaseRepo,
        RewardPointRepositoryInterface $rewardPointRepo
    ) {
        $this->creditPackageRepo = $creditPackageRepo;
        $this->purchaseRepo = $purchaseRepo;
        $this->rewardPointRepo = $rewardPointRepo;
    }

    public function purchasePackage($userId, $packageId)
    {
        return DB::transaction(function () use ($userId, $packageId) {
            $package = $this->creditPackageRepo->find($packageId);
            if (!$package || !$package->is_active) {
                throw new \Exception('Package not found or inactive');
            }
            $purchase = $this->purchaseRepo->create([
                'user_id' => $userId,
                'credit_package_id' => $package->id,
                'credits' => $package->credits,
                'price' => $package->price,
                'reward_points' => $package->reward_points,
                'status' => 'complete',
            ]);
            $user = \App\Models\User::find($userId);
            $user->credits += $package->credits;
            $user->save();
            \App\Models\CreditLog::create([
                'user_id' => $userId,
                'type' => 'purchase',
                'amount' => $package->credits,
                'balance_after' => $user->credits,
                'description' => 'Purchased credit package',
                'reference_id' => $purchase->id,
            ]);
            $this->rewardPointRepo->create([
                'user_id' => $userId,
                'points' => $package->reward_points,
                'type' => 'earn',
                'reference_id' => $purchase->id,
                'description' => 'Reward for purchase',
            ]);
            return $purchase;
        });
    }
} 