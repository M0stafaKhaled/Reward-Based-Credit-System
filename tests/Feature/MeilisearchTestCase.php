<?php

namespace Tests\Feature;

use Tests\TestCase;
use MeiliSearch\Client;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Engines\CollectionEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class MeilisearchTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Always use collection driver for tests by default
        config(['scout.driver' => 'collection']);
        
        // Disable queue for immediate processing
        config(['scout.queue' => false]);
        
        // Disable after commit for immediate processing
        config(['scout.after_commit' => false]);

        // Set up collection driver
        $this->app->singleton(EngineManager::class, function ($app) {
            return new class($app) extends EngineManager {
                public function getDefaultDriver()
                {
                    return 'collection';
                }

                public function createCollectionDriver()
                {
                    return new CollectionEngine;
                }
            };
        });

        // Only try to set up Meilisearch if explicitly configured
        if (env('TEST_USE_MEILISEARCH', false)) {
            $this->setUpMeilisearch();
        }
    }

    protected function setUpMeilisearch(): void
    {
        try {
            $host = env('MEILISEARCH_HOST', 'http://localhost:7700');
            $key = env('MEILISEARCH_KEY', null);

            $client = new Client($host, $key);
            
            // Test connection
            $health = $client->health();
            if ($health['status'] !== 'available') {
                throw new \Exception('Meilisearch is not healthy');
            }

            // If we get here, Meilisearch is working
            config(['scout.driver' => 'meilisearch']);
            config(['scout.meilisearch.host' => $host]);
            config(['scout.meilisearch.key' => $key]);

            // Clean up any existing indices
            $this->cleanupIndices($client);

        } catch (\Exception $e) {
            // If Meilisearch fails, fall back to collection driver
            // but log the error for debugging
            \Log::warning('Meilisearch not available, falling back to collection driver: ' . $e->getMessage());
        }
    }

    protected function cleanupIndices(Client $client): void
    {
        try {
            $indices = $client->getAllIndexes();
            foreach ($indices as $index) {
                $client->deleteIndex($index->getUid());
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to cleanup Meilisearch indices: ' . $e->getMessage());
        }
    }

    protected function refreshIndex(string $model): void
    {
        if (method_exists($model, 'makeAllSearchable')) {
            $model::makeAllSearchable();
        }
    }
} 