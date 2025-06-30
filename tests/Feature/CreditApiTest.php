<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\CreditPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_credit_packages()
    {
        CreditPackage::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/credit-packages');
        $response->assertStatus(200)->assertJson(fn ($json) => $json->has('data', 3));
    }

    public function test_user_can_purchase_credit_package()
    {
        $user = User::factory()->create();
        $package = CreditPackage::factory()->create();
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/purchase', [
            'package_id' => $package->id,
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_purchase_requires_authentication()
    {
        $package = CreditPackage::factory()->create();
        $response = $this->postJson('/api/v1/purchase', [
            'package_id' => $package->id,
        ]);
        $response->assertStatus(401);
    }

    public function test_purchase_requires_valid_package_id()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/purchase', [
            'package_id' => 999,
        ]);
        $response->assertStatus(422);
    }

    public function test_user_can_get_purchase_log()
    {
        $user = \App\Models\User::factory()->create();
        $package = \App\Models\CreditPackage::factory()->create();
        // Simulate a purchase
        $purchase = \App\Models\Purchase::create([
            'user_id' => $user->id,
            'credit_package_id' => $package->id,
            'credits' => $package->credits,
            'price' => $package->price,
            'reward_points' => $package->reward_points,
            'status' => 'complete',
        ]);
        $response = $this->actingAs($user, 'api')->getJson('/api/v1/purchases');
        $response->assertStatus(200)
            ->assertJsonFragment([
                'user_id' => $user->id,
                'credit_package_id' => $package->id,
                'credits' => $package->credits,
                'price' => $package->price,
                'reward_points' => $package->reward_points,
                'status' => 'complete',
            ]);
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'user_id',
                    'credit_package_id',
                    'credits',
                    'price',
                    'reward_points',
                    'status',
                    'created_at',
                    'updated_at',
                    'credit_package' => [
                        'id', 'name', 'credits', 'price', 'reward_points', 'is_active', 'created_at', 'updated_at'
                    ]
                ]
            ]
        ]);
    }

    public function test_user_credit_log_full_cycle()
    {
        // Create a user
        $user = \App\Models\User::factory()->create(['credits' => 0]);

        // Create a product eligible for redemption
        $product = \App\Models\Product::factory()->create(['price_in_points' => 50, 'is_active' => true]);
        \App\Models\OfferPool::factory()->create(['product_id' => $product->id, 'is_active' => true]);

        // Create a credit package that gives enough reward points
        $package = \App\Models\CreditPackage::factory()->create([
            'credits' => 100,
            'reward_points' => 50,
            'is_active' => true,
        ]);

        // User purchases the credit package
        $this->actingAs($user, 'api')->postJson('/api/v1/purchase', [
            'package_id' => $package->id,
        ])->assertStatus(200);

        // User redeems points for the product
        $this->actingAs($user, 'api')->postJson('/api/v1/redeem', [
            'product_id' => $product->id,
        ])->assertStatus(200);

        // Fetch the credit log
        $response = $this->actingAs($user, 'api')->getJson('/api/v1/credit-log');
        $response->assertStatus(200);
        $log = $response->json('data');

        // Assert purchase log exists
        $this->assertTrue(collect($log)->contains(function ($entry) use ($user, $package) {
            return $entry['user_id'] === $user->id
                && $entry['type'] === 'purchase'
                && $entry['amount'] == $package->credits
                && $entry['description'] === 'Purchased credit package';
        }), 'Purchase log entry not found');

        // Assert redeem log exists
        $this->assertTrue(collect($log)->contains(function ($entry) use ($user, $product) {
            return $entry['user_id'] === $user->id
                && $entry['type'] === 'redeem'
                && $entry['amount'] == -$product->price_in_points
                && $entry['description'] === 'Redeemed product';
        }), 'Redeem log entry not found');
    }

    public function test_user_credit_log_redeem_failure()
    {
        // Create a user
        $user = \App\Models\User::factory()->create(['credits' => 0]);

        // Create a product eligible for redemption
        $product = \App\Models\Product::factory()->create(['price_in_points' => 100, 'is_active' => true]);
        \App\Models\OfferPool::factory()->create(['product_id' => $product->id, 'is_active' => true]);

        // User does NOT have enough points (no reward points created)

        // Attempt to redeem points for the product
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/redeem', [
            'product_id' => $product->id,
        ]);
        $response->assertStatus(500); // Exception thrown for insufficient points

        // Fetch the credit log
        $log = \App\Models\CreditLog::where('user_id', $user->id)->where('type', 'redeem')->get();
        $this->assertCount(0, $log, 'Redeem log entry should not be created on failure');
    }
} 