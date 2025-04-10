<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
        Blade::component('customer.layouts.app', 'customer-layout');
        Blade::component('media-display', \App\View\Components\MediaDisplay::class);
        Blade::component('media-upload', \App\View\Components\MediaUpload::class);
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        Blade::component('customer.layouts.app', 'customer-layout');

    }
}
