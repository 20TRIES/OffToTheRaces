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
Route::namespace('\App\ApplicationSetting\Controller')->group(function () {
    Route::get('/time', ['uses' => 'ApplicationTimeController@index', 'as' => 'time.get']);
    Route::put('/time', ['uses' => 'ApplicationTimeController@update', 'as' => 'time.put']);
});

