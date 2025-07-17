<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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
        // Add Content Security Policy headers to all responses
        Response::macro('withCSP', function () {
            return $this->withHeaders([
                'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self';",
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN',
                'X-XSS-Protection' => '1; mode=block',
            ]);
        });
        
        // Apply CSP to all responses
        $this->app->singleton('Illuminate\Contracts\Http\Kernel', function ($app) {
            $kernel = $app->make('App\Http\Kernel');
            $kernel->prependMiddleware('App\Http\Middleware\AddCSPHeaders');
            return $kernel;
        });
    }
}
