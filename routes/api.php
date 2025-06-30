<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    CreditController, RewardController, ProductController,
    AIRecommendationController, AdminController, AdminAuthController, AuthController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // User Authentication
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('user', [AuthController::class, 'user'])->name('auth.user');
    });

    // Credit & Reward
    Route::get('credit-packages', [CreditController::class, 'index'])->name('credit.packages');
    Route::middleware('auth:api')->group(function () {
        Route::post('purchase', [CreditController::class, 'purchase'])->name('credit.purchase');
        Route::get('purchases', [CreditController::class, 'userPurchases'])->name('credit.purchases');
        Route::get('credit-log', [CreditController::class, 'userCreditLog'])->name('credit.log');
        Route::get('points', [RewardController::class, 'points'])->name('reward.points');
        Route::post('redeem', [RewardController::class, 'redeem'])->name('reward.redeem');
        Route::post('ai/recommendation', [AIRecommendationController::class, 'recommend'])->name('ai.recommend');
    });

    // Products (public)
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('products/suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');
    Route::get('products/trending', [ProductController::class, 'trending'])->name('products.trending');
    Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');

    // Admin authentication
    Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login');

    // Admin routes
    Route::middleware('auth:admin-api')->prefix('admin')->group(function () {
        // Credit Packages
        Route::post('credit-packages', [AdminController::class, 'storeCreditPackage'])->name('admin.credit-packages.store');
        Route::put('credit-packages/{id}', [AdminController::class, 'updateCreditPackage'])->name('admin.credit-packages.update');
        Route::delete('credit-packages/{id}', [AdminController::class, 'destroyCreditPackage'])->name('admin.credit-packages.destroy');

        // Products
        Route::post('products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
        Route::put('products/{id}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
        Route::delete('products/{id}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

        // Offer Pool
        Route::post('offers', [AdminController::class, 'storeOffer'])->name('admin.offers.store');
        Route::put('offers/{id}', [AdminController::class, 'updateOffer'])->name('admin.offers.update');
        Route::delete('offers/{id}', [AdminController::class, 'destroyOffer'])->name('admin.offers.destroy');
    });
});
