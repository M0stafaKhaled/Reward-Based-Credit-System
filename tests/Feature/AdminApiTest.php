<?php
namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\CreditPackage;
use App\Models\Product;
use App\Models\OfferPool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_get_token()
    {
        $admin = Admin::factory()->create(['password' => Hash::make('password')]);
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);
        $response->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function test_admin_login_fails_with_wrong_password()
    {
        $admin = Admin::factory()->create(['password' => Hash::make('password')]);
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => $admin->email,
            'password' => 'wrong_password',
        ]);
        $response->assertStatus(401)
            ->assertJson(['message' => 'The provided credentials are incorrect.']);
    }

    public function test_admin_login_fails_with_nonexistent_email()
    {
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(401)
            ->assertJson(['message' => 'The provided credentials are incorrect.']);
    }

    public function test_user_cannot_login_as_admin()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertStatus(401)
            ->assertJson(['message' => 'The provided credentials are incorrect.']);
    }

    public function test_admin_login_validates_input()
    {
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'not-an-email',
            'password' => '',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_admin_can_create_credit_package()
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'admin-api');
        $response = $this->postJson('/api/v1/admin/credit-packages', [
            'name' => 'Gold',
            'credits' => 100,
            'price' => 100,
            'reward_points' => 100,
            'is_active' => true,
        ]);
        $response->assertStatus(201)->assertJson(['data' => ['name' => 'Gold']]);
    }

    public function test_admin_can_update_credit_package()
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'admin-api');
        $package = CreditPackage::factory()->create(['name' => 'Old Package']);
        
        $response = $this->putJson("/api/v1/admin/credit-packages/{$package->id}", [
            'name' => 'Updated Package',
            'credits' => 200,
            'price' => 200,
            'reward_points' => 200,
            'is_active' => true,
        ]);
        
        $response->assertStatus(200)
            ->assertJson(['data' => ['name' => 'Updated Package']]);
    }

    public function test_admin_can_update_product()
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'admin-api');
        $product = Product::factory()->create([
            'name' => 'Old',
            'category' => 'Electronics',
            'price_in_points' => 100
        ]);
        
        $response = $this->putJson('/api/v1/admin/products/' . $product->id, [
            'name' => 'New',
            'category' => 'Electronics',
            'price_in_points' => 100
        ]);
        $response->assertStatus(200)->assertJson(['data' => ['name' => 'New']]);
    }

    public function test_admin_can_delete_offer()
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'admin-api');
        $product = Product::factory()->create();
        $offer = OfferPool::factory()->create(['product_id' => $product->id]);
        $response = $this->deleteJson('/api/v1/admin/offers/' . $offer->id);
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_routes_require_authentication()
    {
        $response = $this->postJson('/api/v1/admin/credit-packages', []);
        $response->assertStatus(401);
    }

    public function test_user_cannot_access_admin_routes()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*'], 'api'); // Note: using 'api' guard for user

        $response = $this->postJson('/api/v1/admin/credit-packages', [
            'name' => 'Gold',
            'credits' => 100,
            'price' => 100,
            'reward_points' => 100,
            'is_active' => true,
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_cannot_access_user_routes()
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'admin-api');

        // Try to access user's points endpoint
        $response = $this->getJson('/api/v1/points');
        $response->assertStatus(401);
    }

    public function test_admin_cannot_delete_nonexistent_offer()
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'admin-api');
        
        $response = $this->deleteJson('/api/v1/admin/offers/999');
        $response->assertStatus(404);
    }

    public function test_admin_cannot_update_nonexistent_product()
    {
        $admin = Admin::factory()->create();
        Sanctum::actingAs($admin, ['*'], 'admin-api');
        
        $response = $this->putJson('/api/v1/admin/products/999', [
            'name' => 'New',
            'category' => 'Electronics',
            'price_in_points' => 100
        ]);
        $response->assertStatus(404);
    }
} 