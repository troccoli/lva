<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\FixturesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])
     ->group(function (): void {
         Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
         Route::resource('seasons', SeasonController::class)->except('show');
         Route::prefix('seasons/{season}')
              ->group(function (): void {
                  Route::resource('competitions', CompetitionController::class)->except('show');
              });
         Route::prefix('competitions/{competition}')
              ->group(function (): void {
                  Route::resource('divisions', DivisionController::class)->except('show');
              });
         Route::get('fixtures', [FixturesController::class, 'index'])->name('fixtures.index');
         Route::resource('clubs', ClubController::class)->except('show');
         Route::prefix('clubs/{club}')
              ->group(function (): void {
                  Route::resource('teams', TeamController::class)->except('show');
              });
         Route::resource('venues', VenueController::class);
     });
