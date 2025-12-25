<?php

namespace App\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'core');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register Blade components
        Blade::componentNamespace('App\\Modules\\Core\\Views\\Components', 'core');
    }
}
