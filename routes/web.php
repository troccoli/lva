<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');

        Route::prefix('data-management')
            ->group(function () {
                Route::get('/seasons', \App\Livewire\Seasons\Index::class)->name('seasons.index');
                Route::get('/seasons/create', \App\Livewire\Seasons\Create::class)->name('seasons.create');
                Route::get('/seasons/show/{season}', \App\Livewire\Seasons\Show::class)->name('seasons.show');
                Route::get('/seasons/update/{season}', \App\Livewire\Seasons\Edit::class)->name('seasons.edit');

                Route::view('competitions', 'competitions')->name('competitions');
                Route::view('divisions', 'divisions')->name('divisions');
                Route::view('fixtures', 'fixtures')->name('fixtures');
                Route::view('clubs', 'clubs')->name('clubs');
                Route::view('teams', 'teams')->name('teams');
                Route::view('venues', 'venues')->name('venues');
            });

        Route::view('appointments', 'appointments')->name('appointments');
        Route::view('people', 'people')->name('people');
    });

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
