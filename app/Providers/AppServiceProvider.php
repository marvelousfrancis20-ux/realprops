<?php

namespace App\Providers;

use App\Models\Amenity;
use App\Models\Category;
use App\Models\City;
use App\Models\Page;
use App\Models\Plan;
use App\Models\PropertyType;
use App\Models\Setting;
use App\Models\State;
use App\Models\Tag;
use App\Policies\AdminOnlyPolicy;
use App\Repositories\Contracts\InquiryRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Eloquent\InquiryRepository;
use App\Repositories\Eloquent\PropertyRepository;
use App\Services\PaymentService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PropertyRepositoryInterface::class, PropertyRepository::class);
        $this->app->bind(InquiryRepositoryInterface::class, InquiryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PaymentService::useCustomModels();

        $this->registerPolicies();
        $this->registerRateLimiters();

        // Surface N+1 queries while developing (spec: preventLazyLoading in dev).
        if (app()->environment('local')) {
            Model::preventLazyLoading();
        }
    }

    private function registerPolicies(): void
    {
        // Master data + settings models share a single admin-only policy.
        foreach ([
            Amenity::class,
            PropertyType::class,
            City::class,
            State::class,
            Category::class,
            Tag::class,
            Page::class,
            Plan::class,
            Setting::class,
        ] as $model) {
            Gate::policy($model, AdminOnlyPolicy::class);
        }
    }

    private function registerRateLimiters(): void
    {
        // API: 60/min authenticated, 30/min guest (spec requirement).
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by('api-user:'.$request->user()->id)
                : Limit::perMinute(30)->by('api-guest:'.($request->ip() ?? 'unknown'));
        });

        // Public contact form.
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(5)
                ->by('contact:'.($request->ip() ?? 'unknown'))
                ->response(fn () => response('Too many contact submissions. Please try again shortly.', 429));
        });

        // Property inquiry form.
        RateLimiter::for('inquiry', function (Request $request) {
            return Limit::perMinute(10)->by('inquiry:'.($request->ip() ?? 'unknown'));
        });

        // Authentication attempts (Breeze "login" limiter keys off this).
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by('login:'.($request->ip() ?? 'unknown').'|'.($request->input('email') ?: ''))
                ->response(fn () => response()->view('auth.throttled', [], 429));
        });

        // API token issuance.
        RateLimiter::for('api-login', function (Request $request) {
            return Limit::perMinute(10)->by('api-login:'.($request->ip() ?? 'unknown'));
        });
    }
}
