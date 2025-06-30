<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Remember data in cache, fallback to DB if Redis fails.
     *
     * @param string $key
     * @param int $ttl
     * @param \Closure $callback
     * @return mixed
     */
    public function remember($key, $ttl, \Closure $callback)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning("Redis unavailable, falling back to DB: " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * Remove a cache entry by key.
     */
    public function forget($key)
    {
        try {
            \Illuminate\Support\Facades\Cache::forget($key);
        } catch (\Exception $e) {
            // Log or ignore
        }
    }

    /**
     * Remove cache entries by pattern (Redis only).
     */
    public function forgetPattern($pattern)
    {
        try {
            $store = \Illuminate\Support\Facades\Cache::getStore();
            if (method_exists($store, 'connection')) {
                $redis = $store->connection();
                $keys = $redis->keys($store->getPrefix() . $pattern);
                foreach ($keys as $key) {
                    $redis->del($key);
                }
            }
        } catch (\Exception $e) {
            // Log or ignore
        }
    }
} 