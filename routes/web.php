<?php

use App\Http\Controllers\AgentPublicController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SavedSearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property:slug}', [PropertyController::class, 'show'])->middleware('recently.viewed')->name('properties.show');
Route::post('/properties/{property:slug}/inquiry', [InquiryController::class, 'store'])
    ->middleware('throttle:inquiry')
    ->name('inquiries.store');
Route::post('/properties/{property:slug}/tour', [InquiryController::class, 'storeTour'])
    ->middleware('throttle:inquiry')
    ->name('tours.store');

Route::get('/agents', [AgentPublicController::class, 'index'])->name('agents.index');
Route::get('/agents/{user}', [AgentPublicController::class, 'show'])->name('agents.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

/*
|--------------------------------------------------------------------------
| Guest / user area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{property}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('/favorites/{property}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');

    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
    Route::post('/compare/{property}', [CompareController::class, 'add'])->name('compare.add');
    Route::delete('/compare/{property}', [CompareController::class, 'remove'])->name('compare.remove');
    Route::delete('/compare', [CompareController::class, 'clear'])->name('compare.clear');

    Route::get('/saved-searches', [SavedSearchController::class, 'index'])->name('saved-searches.index');
    Route::post('/saved-searches', [SavedSearchController::class, 'store'])->name('saved-searches.store');
    Route::patch('/saved-searches/{savedSearch}/toggle', [SavedSearchController::class, 'toggle'])->name('saved-searches.toggle');
    Route::delete('/saved-searches/{savedSearch}', [SavedSearchController::class, 'destroy'])->name('saved-searches.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
