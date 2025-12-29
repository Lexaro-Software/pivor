<?php

namespace App\Modules\Core\Providers;

use App\Modules\Core\Livewire\Dashboard;
use App\Modules\Core\Livewire\NotificationSettings;
use App\Modules\Core\Livewire\RoleForm;
use App\Modules\Core\Livewire\RoleList;
use App\Modules\Core\Livewire\UserForm;
use App\Modules\Core\Livewire\UserList;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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

        // Register Livewire components
        Livewire::component('dashboard', Dashboard::class);
        Livewire::component('notification-settings', NotificationSettings::class);
        Livewire::component('role-form', RoleForm::class);
        Livewire::component('role-list', RoleList::class);
        Livewire::component('user-form', UserForm::class);
        Livewire::component('user-list', UserList::class);
    }
}
