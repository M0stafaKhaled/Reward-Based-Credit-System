<?php
namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;

class ProductApiTest extends MeilisearchTestCase
{
    public function test_can_list_products()
    {
        Product::factory()->count(3)->create();
        $this->refreshIndex(Product::class);

        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('data')
                    ->has('data.0', fn ($json) => 
                        $json->hasAll([
                            'id', 'name', 'description', 'category', 
                            'price_in_points', 'is_active', 'created_at', 'updated_at'
                        ])
                    )
                    ->has('current_page')
                    ->has('first_page_url')
                    ->has('from')
                    ->has('last_page')
                    ->has('last_page_url')
                    ->has('links')
                    ->has('next_page_url')
                    ->has('path')
                    ->has('per_page')
                    ->has('prev_page_url')
                    ->has('to')
                    ->has('total')
                    ->etc()
            );
    }

    public function test_can_show_product()
    {
        $product = Product::factory()->create();
        $this->refreshIndex(Product::class);

        $response = $this->getJson("/api/v1/products/{$product->id}");
        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json->hasAll([
                    'id', 'name', 'description', 'category', 
                    'price_in_points', 'is_active', 'created_at', 'updated_at'
                ])
                ->whereAll([
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'price_in_points' => $product->price_in_points,
                    'is_active' => $product->is_active
                ])
                ->etc()
            );
    }

    public function test_show_product_not_found()
    {
        $response = $this->getJson('/api/v1/products/999');
        $response->assertStatus(404);
    }

    public function test_can_search_products()
    {
        $iphone = Product::factory()->create([
            'name' => 'iPhone',
            'category' => 'Electronics'
        ]);
        
        $samsung = Product::factory()->create([
            'name' => 'Samsung',
            'category' => 'Electronics'
        ]);

        $this->refreshIndex(Product::class);

        $response = $this->getJson('/api/v1/products/search?query=iphone');
        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('data')
                    ->has('data.0', fn ($json) => 
                        $json->hasAll([
                            'id', 'name', 'description', 'category', 
                            'price_in_points', 'is_active', 'created_at', 'updated_at'
                        ])
                        ->where('name', 'iPhone')
                        ->etc()
                    )
                    ->has('current_page')
                    ->has('first_page_url')
                    ->has('from')
                    ->has('last_page')
                    ->has('last_page_url')
                    ->has('links')
                    ->has('next_page_url')
                    ->has('path')
                    ->has('per_page')
                    ->has('prev_page_url')
                    ->has('to')
                    ->has('total')
                    ->etc()
            );
    }

 

   
} 