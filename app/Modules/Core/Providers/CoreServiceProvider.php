<?php

namespace App\Modules\Core\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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
        Route::middleware('web')
            ->group(__DIR__ . '/../routes/web.php');

        // Register Blade components
        Blade::componentNamespace('App\\Modules\\Core\\Views\\Components', 'core');
    }
}
