<?php

use App\Modules\Clients\Livewire\ClientForm;
use App\Modules\Clients\Livewire\ClientList;
use App\Modules\Clients\Livewire\ClientShow;
use Illuminate\Support\Facades\Route;

Route::get('/', ClientList::class)->name('index');
Route::get('/create', ClientForm::class)->name('create');
Route::get('/{client}', ClientShow::class)->name('show');
Route::get('/{client}/edit', ClientForm::class)->name('edit');
