<?php
namespace App\Services;

use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;

class ProductService
{
    protected $productRepo;
    protected $cacheService;

    public function __construct(ProductRepositoryInterface $productRepo, CacheService $cacheService)
    {
        $this->productRepo = $productRepo;
        $this->cacheService = $cacheService;
    }

    public function getAllProducts($perPage = 15)
    {
        return $this->productRepo->allPaginated($perPage);
    }

    public function getTrendingProducts($category = null, $limit = 10)
    {
        return $this->productRepo->getTrending($category, $limit);
    }

    public function searchProducts($query, $category = null, $page = 1, $perPage = 20)
    {
        return $this->productRepo->search($query, $category, $perPage);
    }
} 