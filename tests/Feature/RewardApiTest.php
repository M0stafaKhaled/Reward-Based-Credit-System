<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\RewardPoint;
use App\Models\OfferPool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RewardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_points()
    {
        $user = User::factory()->create();
        RewardPoint::factory()->create(['user_id' => $user->id, 'points' => 100, 'type' => 'earn']);
        $response = $this->actingAs($user, 'api')->getJson('/api/v1/points');
        $response->assertStatus(200)->assertJson(['points' => 100]);
    }

    public function test_user_can_redeem_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price_in_points' => 50]);
        OfferPool::factory()->create(['product_id' => $product->id, 'is_active' => true]);
        RewardPoint::factory()->create(['user_id' => $user->id, 'points' => 100, 'type' => 'earn']);
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/redeem', [
            'product_id' => $product->id,
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_redeem_requires_authentication()
    {
        $response = $this->postJson('/api/v1/redeem', [
            'product_id' => 1,
        ]);
        $response->assertStatus(401);
    }

    public function test_redeem_requires_valid_product_id()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/redeem', [
            'product_id' => 999,
        ]);
        $response->assertStatus(422);
    }

    public function test_credit_log_created_on_redeem()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create(['price_in_points' => 50]);
        \App\Models\OfferPool::factory()->create(['product_id' => $product->id, 'is_active' => true]);
        \App\Models\RewardPoint::factory()->create(['user_id' => $user->id, 'points' => 100, 'type' => 'earn']);
        $this->actingAs($user, 'api')->postJson('/api/v1/redeem', [
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseHas('credit_logs', [
            'user_id' => $user->id,
            'type' => 'redeem',
            'amount' => -50,
            'description' => 'Redeemed product',
            'reference_id' => $product->id,
        ]);
    }
}
