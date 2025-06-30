<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CreditPackageRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\OfferPoolRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\CreditPackageRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\OfferRequest;
use App\Services\CacheService;
use App\Http\Resources\CreditPackageResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\OfferPoolResource;

class AdminController extends Controller
{
    protected $creditPackageRepo;
    protected $productRepo;
    protected $offerPoolRepo;
    protected $cacheService;

    public function __construct(
        CreditPackageRepositoryInterface $creditPackageRepo,
        ProductRepositoryInterface $productRepo,
        OfferPoolRepositoryInterface $offerPoolRepo,
        CacheService $cacheService
    ) {
        $this->creditPackageRepo = $creditPackageRepo;
        $this->productRepo = $productRepo;
        $this->offerPoolRepo = $offerPoolRepo;
        $this->cacheService = $cacheService;
    }

    // Credit Packages CRUD
    public function storeCreditPackage(CreditPackageRequest $request)
    {
        $package = $this->creditPackageRepo->create($request->validated());
        return new CreditPackageResource($package);
    }
    public function updateCreditPackage(CreditPackageRequest $request, $id)
    {
        $package = $this->creditPackageRepo->update($id, $request->validated());
        return new CreditPackageResource($package);
    }
    public function destroyCreditPackage($id)
    {
        $this->creditPackageRepo->delete($id);
        return response()->json(['success' => true]);
    }

    // Products CRUD
    public function storeProduct(ProductRequest $request)
    {
        $product = $this->productRepo->create($request->validated());
        return new ProductResource($product);
    }
    public function updateProduct(ProductRequest $request, $id)
    {
        $product = $this->productRepo->update($id, $request->validated());
        return new ProductResource($product);
    }
    public function destroyProduct($id)
    {
        $this->productRepo->delete($id);
        return response()->json(['success' => true]);
    }

    // Offer Pool CRUD
    public function storeOffer(OfferRequest $request)
    {
        $offer = $this->offerPoolRepo->create($request->validated());
        return new OfferPoolResource($offer);
    }
    public function updateOffer(OfferRequest $request, $id)
    {
        $offer = $this->offerPoolRepo->update($id, $request->validated());
        return new OfferPoolResource($offer);
    }
    public function destroyOffer($id)
    {
        $result = $this->offerPoolRepo->delete($id);
        if (!$result) {
            return response()->json(['message' => 'Offer not found'], 404);
        }
        return response()->json(['success' => true]);
    }
} 