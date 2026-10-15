<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PropertyController;
use App\Models\Plan;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:api-login')->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:api-login')->name('api.login');

    Route::get('/properties', [PropertyController::class, 'index'])->name('api.properties.index');
    Route::get('/properties/featured', [PropertyController::class, 'featured'])->name('api.properties.featured');
    Route::get('/properties/search', [PropertyController::class, 'search'])->name('api.properties.search');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('api.properties.show');

    Route::get('/agents', [PropertyController::class, 'agents'])->name('api.agents.index');
    Route::get('/agents/{id}', [PropertyController::class, 'agent'])->name('api.agents.show');

    Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
    Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('api.posts.show');
    Route::get('/categories', [PostController::class, 'categories'])->name('api.categories.index');
    Route::get('/tags', [PostController::class, 'tags'])->name('api.tags.index');

    Route::get('/plans', function () {
        return Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get(['id', 'name', 'slug', 'description', 'price', 'currency', 'billing_interval', 'listing_limit', 'allows_featured', 'features']);
    })->name('api.plans.index');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::patch('/me', [AuthController::class, 'updateProfile'])->name('api.me.update');

        Route::get('/favorites', [FavoriteController::class, 'index'])->name('api.favorites.index');
        Route::post('/favorites/{property}', [FavoriteController::class, 'toggle'])->name('api.favorites.toggle');
        Route::delete('/favorites/{property}', [FavoriteController::class, 'destroy'])->name('api.favorites.destroy');

        Route::post('/inquiries', [InquiryController::class, 'store'])->name('api.inquiries.store');
        Route::get('/inquiries', [InquiryController::class, 'mine'])->name('api.inquiries.mine');
        Route::post('/tours/{propertyId}', [InquiryController::class, 'scheduleTour'])->name('api.tours.store');
    });
});

Route::post('/webhooks/stripe', function (Request $request, PaymentService $payments) {
    $payments->handleWebhook(
        json_decode($request->getContent(), false) ?: (object) $request->all()
    );

    return response()->json(['received' => true]);
})->name('api.webhooks.stripe');
