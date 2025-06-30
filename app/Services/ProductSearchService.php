<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductSearchService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const ITEMS_PER_PAGE = 20;

    /**
     * Search for products with caching
     */
    public function search(string $query = '', ?string $category = null, int $page = 1, array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? self::ITEMS_PER_PAGE;
        $searchQuery = Product::search($query);
        if ($category) {
            $searchQuery->where('category', $category);
        }
        // Apply filters
        foreach ($filters as $field => $value) {
            if (in_array($field, ['price_in_points', 'is_active'])) {
                $searchQuery->where($field, $value);
            }
        }
        return $searchQuery->paginate($perPage, 'page', $page);
    }

    /**
     * Get product suggestions based on partial input
     */
    public function getSuggestions(string $query, int $limit = 5)
    {
        return Product::search($query)
            ->where('is_active', true)
            ->take($limit)
            ->get();
    }

    /**
     * Get trending products based on category
     */
    public function getTrendingProducts(?string $category = null, int $limit = 10): array
    {
        $query = Product::search('')->where('is_active', true);
        if ($category) {
            $query->where('category', $category);
        }
        return $query->take($limit)->get()->toArray();
    }

    /**
     * Generate a unique cache key based on search parameters
     */
    private function generateCacheKey(string $query, ?string $category, int $page, array $filters): string
    {
        $filterString = collect($filters)->map(fn($value, $key) => "{$key}:{$value}")->implode('|');
        return "product_search:{$query}:{$category}:{$page}:{$filterString}";
    }

    /**
     * Clear search cache
     */
    public function clearCache(): void
    {
        Cache::tags(['product_search'])->flush();
    }
} 