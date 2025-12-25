<?php

namespace App\Modules\Communications\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class CommunicationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'communications');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->registerRoutes();
        $this->registerLivewireComponents();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('communications')
            ->name('communications.')
            ->group(__DIR__ . '/../routes/web.php');
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('communications.communication-list', \App\Modules\Communications\Livewire\CommunicationList::class);
        Livewire::component('communications.communication-form', \App\Modules\Communications\Livewire\CommunicationForm::class);
    }
}
