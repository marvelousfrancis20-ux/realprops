<?php

use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\InquiryController;
use App\Http\Controllers\Agent\ProfileController;
use App\Http\Controllers\Agent\PropertyController;
use App\Http\Controllers\Agent\SubscriptionController;
use App\Http\Controllers\Agent\TourRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:agent', 'active'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    Route::resource('properties', PropertyController::class)->except(['show'])->parameters([
        'properties' => 'property',
    ]);
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');

    Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
    Route::post('/inquiries/{inquiry}/reply', [InquiryController::class, 'reply'])
        ->middleware('throttle:inquiry')
        ->name('inquiries.reply');
    Route::patch('/inquiries/{inquiry}', [InquiryController::class, 'update'])->name('inquiries.update');
    Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');

    Route::get('/tours', [TourRequestController::class, 'index'])->name('tours.index');
    Route::patch('/tours/{tour}', [TourRequestController::class, 'update'])->name('tours.update');

    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/{plan}/checkout', [SubscriptionController::class, 'checkout'])
        ->name('subscription.checkout');
    Route::post('/subscription/portal', [SubscriptionController::class, 'portal'])
        ->name('subscription.portal');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
