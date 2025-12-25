<?php

namespace App\Modules\Contacts\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class ContactsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'contacts');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->registerRoutes();
        $this->registerLivewireComponents();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('contacts')
            ->name('contacts.')
            ->group(__DIR__ . '/../routes/web.php');
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('contacts.contact-list', \App\Modules\Contacts\Livewire\ContactList::class);
        Livewire::component('contacts.contact-form', \App\Modules\Contacts\Livewire\ContactForm::class);
        Livewire::component('contacts.contact-show', \App\Modules\Contacts\Livewire\ContactShow::class);
    }
}
