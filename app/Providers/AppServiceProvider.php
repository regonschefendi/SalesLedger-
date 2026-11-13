<?php

namespace App\Providers;

use App\Auth\CachedUserProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Auth::provider('cached-eloquent', function (Application $app, array $config) {
            return new CachedUserProvider($app['hash'], $config['model']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pakai HTTPS kalau lagi di production (Railway)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');

            // Cookie sesi hanya dikirim lewat HTTPS agar tidak bisa dicuri
            // saat koneksi HTTP (session hijacking).
            config(['session.secure' => true]);
        }
    }
}
