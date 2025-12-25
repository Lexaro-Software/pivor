<?php

use App\Modules\Communications\Livewire\CommunicationForm;
use App\Modules\Communications\Livewire\CommunicationList;
use Illuminate\Support\Facades\Route;

Route::get('/', CommunicationList::class)->name('index');
Route::get('/create', CommunicationForm::class)->name('create');
Route::get('/{communication}/edit', CommunicationForm::class)->name('edit');
