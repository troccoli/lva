<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::group(['middleware' => 'web'], function () {

    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@showHome']);

    /*
     |-------------------------------------------------------------------------
     | Authentication Routes
     |-------------------------------------------------------------------------
     | We don't use Route::auth() because that does not define names for the routes
     | Instead, the routes defined in Route::auth() are copied here (and assigned a name)
     */
    //Route::auth();
    // Authentication Routes...
    Route::group(['namespace' => 'Auth'], function () {
        Route::get('login', ['as' => 'login', 'uses' => 'AuthController@showLoginForm']);
        Route::post('login', ['as' => 'loginSubmit', 'uses' => 'AuthController@login']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);

        // Registration Routes...
        Route::get('register', ['as' => 'register', 'uses' => 'AuthController@showRegistrationForm']);
        Route::post('register', ['as' => 'registerSubmit', 'uses' => 'AuthController@register']);

        // Password Reset Routes...
        Route::get('password/reset/{token?}', ['as' => 'passwordReset', 'uses' => 'PasswordController@showResetForm']);
        Route::post('password/email', ['as' => 'passwordResetEmail', 'uses' => 'PasswordController@sendResetLinkEmail']);
        Route::post('password/reset', ['as' => 'passwordResetSubmit', 'uses' => 'PasswordController@reset']);
    });
});
