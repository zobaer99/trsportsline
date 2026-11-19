<?php

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\HeroBannerController;
use App\Http\Controllers\Admin\StoreSettingsController;
use App\Http\Controllers\CustomOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\Admin\StoreContentController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\TrackingSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/delivery-zones', [StorefrontController::class, 'deliveryZones']);
Route::get('/banners', [StorefrontController::class, 'banners']);
Route::get('/content/{page}', [StorefrontController::class, 'sections']);
Route::get('/pricing', [PricingController::class, 'index']);
Route::get('/settings/storefront', [StorefrontController::class, 'settings']);
Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:30,1');
Route::post('/orders/track', [OrderController::class, 'track'])->middleware('throttle:20,1');
Route::post('/custom-orders', [CustomOrderController::class, 'store'])->middleware('throttle:10,1');
Route::get('/settings/tracking', [TrackingSettingsController::class, 'publicSettings']);
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:8,1');
Route::post('/account/register', [AccountController::class, 'register'])->middleware('throttle:6,1');
Route::post('/account/login', [AccountController::class, 'login'])->middleware('throttle:8,1');
Route::post('/account/guest-login', [AccountController::class, 'guestLogin'])->middleware('throttle:8,1');
Route::middleware('auth:sanctum')->prefix('account')->group(function () {
    Route::post('/logout', [AccountController::class, 'logout']);
    Route::get('/profile', [AccountController::class, 'profile']);
    Route::put('/profile', [AccountController::class, 'updateProfile']);
    Route::put('/password', [AccountController::class, 'updatePassword']);
    Route::get('/orders', [AccountController::class, 'orders']);
});
Route::get('/tracking/capi-settings', [TrackingSettingsController::class, 'capiSettings'])->middleware('tracking.service');

Route::middleware(['auth:sanctum', 'admin.user'])->prefix('admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::apiResource('products', AdminProductController::class);
    Route::apiResource('banners', HeroBannerController::class);
    Route::apiResource('coupons', CouponController::class);
    Route::apiResource('reviews', AdminReviewController::class);
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update']);
    Route::get('/settings', [StoreSettingsController::class, 'index']);
    Route::put('/settings', [StoreSettingsController::class, 'update']);
    Route::get('/pricing', [PricingController::class, 'adminIndex']);
    Route::put('/pricing', [PricingController::class, 'update']);
    Route::get('/sections', [StoreContentController::class, 'index']);
    Route::put('/sections', [StoreContentController::class, 'update']);
    Route::get('/media', [MediaController::class, 'index']);
    Route::post('/media', [MediaController::class, 'store']);
});
