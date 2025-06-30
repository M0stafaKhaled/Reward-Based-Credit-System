<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CreditPackageRepositoryInterface;
use App\Services\CreditService;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Cache;
use App\Models\IdempotencyKey;
use App\Http\Resources\PurchaseResource;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CreditPackageResource;
use App\Http\Resources\CreditLogResource;

class CreditController extends Controller
{
    protected $creditService;
    protected $creditPackageRepo;

    public function __construct(CreditService $creditService, CreditPackageRepositoryInterface $creditPackageRepo)
    {
        $this->creditService = $creditService;
        $this->creditPackageRepo = $creditPackageRepo;
    }

    public function index()
    {
        $packages = $this->creditPackageRepo->all();
        return CreditPackageResource::collection($packages)->response();
    }

    public function purchase(PurchaseRequest $request)
    {
        $userId = $request->user()->id;
        try {
            $purchase = $this->creditService->purchasePackage($userId, $request->package_id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Purchase failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['success' => true, 'purchase' => new \App\Http\Resources\PurchaseResource($purchase)]);
    }

    public function userPurchases(Request $request)
    {
        $user = $request->user();
        $purchases = \App\Models\Purchase::where('user_id', $user->id)
            ->with('creditPackage')
            ->orderByDesc('created_at')
            ->get();
        return PurchaseResource::collection($purchases)->response();
    }

    public function userCreditLog(Request $request)
    {
        $user = $request->user();
        $logs = \App\Models\CreditLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        return CreditLogResource::collection($logs)->response();
    }
} 