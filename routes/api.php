<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')
    ->prefix('v1')
    ->namespace('Api\V1')
    ->group(function (): void {
        Route::get('seasons', 'SeasonController@all');
        Route::get('seasons/{season}', 'SeasonController@get');

        Route::get('competitions', 'CompetitionController@all');
        Route::get('competitions/{competition}', 'CompetitionController@get');

        Route::get('divisions', 'DivisionController@all');
        Route::get('divisions/{division}', 'DivisionController@get');

        Route::get('clubs', 'ClubController@all');
        Route::get('clubs/{club}', 'ClubController@get');

        Route::get('teams', 'TeamController@all');
        Route::get('teams/{team}', 'TeamController@get');

        Route::get('venues', 'VenueController@all');
        Route::get('venues/{venue}', 'VenueController@get');

        Route::get('fixtures', 'FixturesController@all');
    });
