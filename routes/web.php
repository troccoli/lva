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

                Route::get('/competitions', \App\Livewire\Competitions\Index::class)->name('competitions.index');
                Route::get('/competitions/create', \App\Livewire\Competitions\Create::class)->name('competitions.create');
                Route::get('/competitions/show/{competition}', \App\Livewire\Competitions\Show::class)->name('competitions.show');
                Route::get('/competitions/update/{competition}', \App\Livewire\Competitions\Edit::class)->name('competitions.edit');

                Route::get('/divisions', \App\Livewire\Divisions\Index::class)->name('divisions.index');
                Route::get('/divisions/create', \App\Livewire\Divisions\Create::class)->name('divisions.create');
                Route::get('/divisions/show/{division}', \App\Livewire\Divisions\Show::class)->name('divisions.show');
                Route::get('/divisions/update/{division}', \App\Livewire\Divisions\Edit::class)->name('divisions.edit');

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
