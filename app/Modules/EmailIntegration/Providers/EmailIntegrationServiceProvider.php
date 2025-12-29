<?php

namespace App\Modules\EmailIntegration\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class EmailIntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'email-integration');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->registerRoutes();
        $this->registerLivewireComponents();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->group(__DIR__ . '/../routes/web.php');
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('email-integration.email-settings', \App\Modules\EmailIntegration\Livewire\EmailSettings::class);
        Livewire::component('email-integration.compose-email', \App\Modules\EmailIntegration\Livewire\ComposeEmail::class);
    }
}
