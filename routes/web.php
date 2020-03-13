<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');

Route::middleware(['auth', 'verified'])
    ->group(function (): void {
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
        Route::middleware(['can:manage raw data'])
            ->group(function (): void {
                Route::resource('seasons', 'SeasonController')->except('show');
                Route::prefix('seasons/{season}')
                    ->group(function (): void {
                        Route::resource('competitions', 'CompetitionController')->except('show');
                    });
                Route::prefix('competitions/{competition}')
                    ->group(function (): void {
                        Route::resource('divisions', 'DivisionController')->except('show');
                    });
                Route::resource('clubs', 'ClubController')->except('show');
                Route::prefix('clubs/{club}')
                    ->group(function (): void {
                        Route::resource('teams', 'TeamController')->except('show');
                    });
                Route::resource('venues', 'VenueController');
                Route::get('fixtures')
                    ->uses('FixturesController@index')
                    ->name('fixtures.index');
            });
    });
