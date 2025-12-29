<?php

use Illuminate\Support\Facades\Route;
use App\Modules\EmailIntegration\Livewire\EmailSettings;
use App\Modules\EmailIntegration\Http\Controllers\OAuthController;

// Email Settings page
Route::get('/settings/email', EmailSettings::class)->name('settings.email');

// OAuth callbacks
Route::get('/email/oauth/google/callback', [OAuthController::class, 'googleCallback'])
    ->name('email.oauth.google.callback');
Route::get('/email/oauth/microsoft/callback', [OAuthController::class, 'microsoftCallback'])
    ->name('email.oauth.microsoft.callback');
