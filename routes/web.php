<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
        ->name('dashboard');
    Route::prefix('/data-management')
        ->group(function () {
            Route::get('/seasons', fn () => Inertia::render('Seasons'))
                ->name('seasons');
            Route::get('/competitions', fn () => Inertia::render('Competitions'))
                ->name('competitions');
            Route::get('/divisions', fn () => Inertia::render('Divisions'))
                ->name('divisions');
            Route::get('/fixtures', fn () => Inertia::render('Fixtures'))
                ->name('fixtures');
            Route::get('/clubs', fn () => Inertia::render('Clubs'))
                ->name('clubs');
            Route::get('/teams', fn () => Inertia::render('Teams'))
                ->name('teams');
            Route::get('/venues', fn () => Inertia::render('Venues'))
                ->name('venues');
        });
    Route::get('/results', fn () => Inertia::render('Results'))
        ->name('results');
    Route::get('/appointments', fn () => Inertia::render('Appointments'))
        ->name('appointments');
    Route::get('/people', fn () => Inertia::render('People'))
        ->name('people');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
