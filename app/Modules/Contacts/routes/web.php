<?php

use App\Modules\Contacts\Livewire\ContactForm;
use App\Modules\Contacts\Livewire\ContactList;
use App\Modules\Contacts\Livewire\ContactShow;
use Illuminate\Support\Facades\Route;

Route::get('/', ContactList::class)->name('index');
Route::get('/create', ContactForm::class)->name('create');
Route::get('/{contact}', ContactShow::class)->name('show');
Route::get('/{contact}/edit', ContactForm::class)->name('edit');
