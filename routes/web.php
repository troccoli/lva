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
                Route::get('/competitions/create',
                    \App\Livewire\Competitions\Create::class)->name('competitions.create');
                Route::get('/competitions/show/{competition}',
                    \App\Livewire\Competitions\Show::class)->name('competitions.show');
                Route::get('/competitions/update/{competition}',
                    \App\Livewire\Competitions\Edit::class)->name('competitions.edit');

                Route::get('/divisions', \App\Livewire\Divisions\Index::class)->name('divisions.index');
                Route::get('/divisions/create', \App\Livewire\Divisions\Create::class)->name('divisions.create');
                Route::get('/divisions/show/{division}', \App\Livewire\Divisions\Show::class)->name('divisions.show');
                Route::get('/divisions/update/{division}', \App\Livewire\Divisions\Edit::class)->name('divisions.edit');

                Route::get('/fixtures', \App\Livewire\Fixtures\Index::class)->name('fixtures.index');
                Route::get('/fixtures/create', \App\Livewire\Fixtures\Create::class)->name('fixtures.create');
                Route::get('/fixtures/show/{fixture}', \App\Livewire\Fixtures\Show::class)->name('fixtures.show');
                Route::get('/fixtures/update/{fixture}', \App\Livewire\Fixtures\Edit::class)->name('fixtures.edit');

                Route::get('/clubs', \App\Livewire\Clubs\Index::class)->name('clubs.index');
                Route::get('/clubs/create', \App\Livewire\Clubs\Create::class)->name('clubs.create');
                Route::get('/clubs/show/{club}', \App\Livewire\Clubs\Show::class)->name('clubs.show');
                Route::get('/clubs/update/{club}', \App\Livewire\Clubs\Edit::class)->name('clubs.edit');

                Route::get('/teams', \App\Livewire\Teams\Index::class)->name('teams.index');
                Route::get('/teams/create', \App\Livewire\Teams\Create::class)->name('teams.create');
                Route::get('/teams/show/{team}', \App\Livewire\Teams\Show::class)->name('teams.show');
                Route::get('/teams/update/{team}', \App\Livewire\Teams\Edit::class)->name('teams.edit');

                Route::get('/venues', \App\Livewire\Venues\Index::class)->name('venues.index');
                Route::get('/venues/create', \App\Livewire\Venues\Create::class)->name('venues.create');
                Route::get('/venues/show/{venue}', \App\Livewire\Venues\Show::class)->name('venues.show');
                Route::get('/venues/update/{venue}', \App\Livewire\Venues\Edit::class)->name('venues.edit');
            });

        Route::view('appointments', 'appointments')->name('appointments');
        Route::view('people', 'people')->name('people');
    });

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
