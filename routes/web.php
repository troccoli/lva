<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::middleware('auth')
    ->group(function (): void {
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    });
