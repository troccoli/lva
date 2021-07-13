<?php

use App\Http\Controllers\Api\V1\ClubController;
use App\Http\Controllers\Api\V1\CompetitionController;
use App\Http\Controllers\Api\V1\DivisionController;
use App\Http\Controllers\Api\V1\FixturesController;
use App\Http\Controllers\Api\V1\SeasonController;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\Api\V1\VenueController;

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
     ->group(function (): void {
         Route::get('seasons', [SeasonController::class, 'all']);
         Route::get('seasons/{season}', [SeasonController::class, 'get']);

         Route::get('competitions', [CompetitionController::class, 'all']);
         Route::get('competitions/{competition}', [CompetitionController::class, 'get']);

         Route::get('divisions', [DivisionController::class, 'all']);
         Route::get('divisions/{division}', [DivisionController::class, 'get']);

         Route::get('clubs', [ClubController::class, 'all']);
         Route::get('clubs/{club}', [ClubController::class, 'get']);

         Route::get('teams', [TeamController::class, 'all']);
         Route::get('teams/{team}', [TeamController::class, 'get']);

         Route::get('venues', [VenueController::class, 'all']);
         Route::get('venues/{venue}', [VenueController::class, 'get']);

         Route::get('fixtures', [FixturesController::class, 'all']);
     });
