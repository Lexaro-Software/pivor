<?php

use App\Modules\Core\Livewire\Dashboard;
use App\Modules\Core\Livewire\DataExport;
use App\Modules\Core\Livewire\DataImport;
use App\Modules\Core\Livewire\RoleForm;
use App\Modules\Core\Livewire\RoleList;
use App\Modules\Core\Livewire\UserForm;
use App\Modules\Core\Livewire\UserList;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Import/Export
    Route::get('/import', DataImport::class)->name('import');
    Route::get('/export', DataExport::class)->name('export');

    // Admin-only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', UserList::class)->name('users.index');
        Route::get('/users/create', UserForm::class)->name('users.create');
        Route::get('/users/{user}/edit', UserForm::class)->name('users.edit');

        Route::get('/roles', RoleList::class)->name('roles.index');
        Route::get('/roles/create', RoleForm::class)->name('roles.create');
        Route::get('/roles/{role}/edit', RoleForm::class)->name('roles.edit');
    });
});

// Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (auth()->attempt($credentials, request()->boolean('remember'))) {
        request()->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->middleware('guest');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');
