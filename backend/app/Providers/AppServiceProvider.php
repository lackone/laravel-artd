<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\AdminAuth;
use App\Models\AdminRole;
use App\Observers\AdminAuthObserver;
use App\Observers\AdminObserver;
use App\Observers\AdminRoleObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (app()->environment() == 'local' || app()->environment() == 'testing' || app()->environment() == 'dev') {
            error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
        } else {
            error_reporting(0);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(99999)->by($request->user()?->id ?: $request->ip());
        });

        AdminAuth::observe(AdminAuthObserver::class);
        AdminRole::observe(AdminRoleObserver::class);
        Admin::observe(AdminObserver::class);
    }
}
