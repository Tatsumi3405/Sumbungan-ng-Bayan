<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Behind a TLS-terminating proxy (Render) trust forwarded headers so
        // Laravel knows the request was HTTPS and builds https:// URLs.
        if (config('app.env') === 'production' || env('FORCE_HTTPS')) {
            Request::setTrustedProxies(
                ['*'],
                Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO
            );
            URL::forceScheme('https');
        }
    }
}
