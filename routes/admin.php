<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin', 'active'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::post('/properties/{property}/approve', [PropertyController::class, 'approve'])->name('properties.approve');
    Route::post('/properties/{property}/reject', [PropertyController::class, 'reject'])->name('properties.reject');
    Route::post('/properties/{property}/feature', [PropertyController::class, 'feature'])->name('properties.feature');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
    Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show');
    Route::post('/agents/{agent}/verify', [AgentController::class, 'verify'])->name('agents.verify');
    Route::post('/agents/{agent}/unverify', [AgentController::class, 'unverify'])->name('agents.unverify');

    foreach (['amenities', 'types', 'states', 'cities', 'categories', 'tags'] as $resource) {
        Route::get($resource, [MasterDataController::class, 'index'])->defaults('resource', $resource)->name("{$resource}.index");
        Route::get($resource.'/create', [MasterDataController::class, 'create'])->defaults('resource', $resource)->name("{$resource}.create");
        Route::post($resource, [MasterDataController::class, 'store'])->defaults('resource', $resource)->name("{$resource}.store");
        Route::get($resource.'/{id}/edit', [MasterDataController::class, 'edit'])->defaults('resource', $resource)->name("{$resource}.edit");
        Route::put($resource.'/{id}', [MasterDataController::class, 'update'])->defaults('resource', $resource)->name("{$resource}.update");
        Route::delete($resource.'/{id}', [MasterDataController::class, 'destroy'])->defaults('resource', $resource)->name("{$resource}.destroy");
    }

    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');

    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');

    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{contact_message}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::patch('/contact-messages/{contact_message}', [ContactMessageController::class, 'update'])->name('contact-messages.update');
    Route::delete('/contact-messages/{contact_message}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
});
