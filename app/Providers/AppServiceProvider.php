<?php
namespace App\Providers;
use Laravel\Passport\Passport;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\AdminPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::loadKeysFrom(storage_path('oauth-keys'));
        Passport::authorizationView('authorize');
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(20)->by($request->header('Authorization'));
        });
        Gate::define('onlyAllowAdmin', [AdminPolicy::class, 'onlyAllowAdmin']);
    }
}
