<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use Illuminate\Redis\RedisManager;
use Exception;

class FallbackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register a custom Redis manager if Redis is unavailable
        $this->app->extend('redis', function ($redis, $app) {
            try {
                // Test the connection (will throw an exception if Redis is down)
                $redis->connection()->ping();
                return $redis;
            } catch (Exception $e) {
                // Redis is not available, use fallback configuration
                $this->configureFallbacks();

                // Create a fake Redis manager that acts as a null object
                return new class($app, 'none', []) extends RedisManager {
                    public function connection($name = null) {
                        return new class() {
                            public function __call($method, $parameters) { return null; }
                        };
                    }
                };
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Check if Redis is available early in the boot process
        try {
            // Try to instantiate Predis client directly
            if (class_exists('Predis\Client')) {
                $client = new \Predis\Client([
                    'host' => config('database.redis.default.host', '127.0.0.1'),
                    'port' => config('database.redis.default.port', 6379),
                    'timeout' => 1,
                ]);
                $client->ping();
            } else if (extension_loaded('redis')) {
                $redis = new \Redis();
                $redis->connect(
                    config('database.redis.default.host', '127.0.0.1'),
                    config('database.redis.default.port', 6379),
                    1
                );
                $redis->ping();
            } else {
                throw new Exception('No Redis client available');
            }
        } catch (Exception $e) {
            // Redis is not available, use fallback drivers
            $this->configureFallbacks();
        }
    }

    /**
     * Configure fallback drivers when Redis is unavailable
     */
    protected function configureFallbacks(): void
    {
        // Replace Redis driver with alternative drivers for all services
        config(['cache.default' => 'file']);
        config(['queue.default' => 'sync']);
        config(['session.driver' => 'file']);
        config(['broadcasting.default' => 'log']);

        // Disable Redis in database config to prevent further connection attempts
        config(['database.redis.client' => 'null']);

        // Rebind essential services to use the new configuration
        if ($this->app->bound('cache')) {
            $this->app->forgetInstance('cache');
            $this->app->forgetInstance('cache.store');
        }

        if ($this->app->bound('queue')) {
            $this->app->forgetInstance('queue');
            $this->app->forgetInstance('queue.connection');
        }

        if ($this->app->bound('session')) {
            $this->app->forgetInstance('session');
            $this->app->forgetInstance('session.store');
        }

        // Log the fallback configuration
        \Illuminate\Support\Facades\Log::info('Redis is unavailable, using fallback configurations.');
    }
}
