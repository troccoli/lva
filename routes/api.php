<?php

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

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api', 'namespace' => 'v1'], function () {
    Route::get('uploads/status.json', ['as' => 'upload-status', 'uses' => 'UploadApiController@getUploadStatus']);
    Route::post('maps/team', ['as' => 'loading-map-team', 'uses' => 'UploadApiController@mapTeam']);
    Route::post('maps/venue', ['as' => 'loading-map-venue', 'uses' => 'UploadApiController@mapVenue']);
    Route::get('uploads/resume', ['as' => 'resume-upload', 'uses' => 'UploadApiController@resumeUpload']);
    Route::get('uploads/abandon', ['as' => 'abandon-upload', 'uses' => 'UploadApiController@abandonUpload']);
});
