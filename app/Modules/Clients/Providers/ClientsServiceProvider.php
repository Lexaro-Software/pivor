<?php

namespace App\Modules\Clients\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class ClientsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'clients');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->registerRoutes();
        $this->registerLivewireComponents();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('clients')
            ->name('clients.')
            ->group(__DIR__ . '/../routes/web.php');
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('clients.client-list', \App\Modules\Clients\Livewire\ClientList::class);
        Livewire::component('clients.client-form', \App\Modules\Clients\Livewire\ClientForm::class);
        Livewire::component('clients.client-show', \App\Modules\Clients\Livewire\ClientShow::class);
    }
}
