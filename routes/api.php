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

// FIXME: find a way set throttle using configuration

Route::middleware('throttle:500,1')->namespace('Api')->group(function () {
    Route::post('/status/check', 'StatusController@check');
    Route::post('/status', 'StatusController@index');
});
