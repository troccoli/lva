<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');

Route::middleware(['auth', 'verified'])
    ->group(function (): void {
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
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
    });
