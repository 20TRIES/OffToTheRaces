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
Route::namespace('\App\ApplicationSetting\Http\Controller')->group(function () {
    Route::get('/time', ['uses' => 'ApplicationTimeController@index', 'as' => 'time.get']);
    Route::put('/time', ['uses' => 'ApplicationTimeController@update', 'as' => 'time.put']);
});

Route::namespace('\App\Race\Http\Controller')->group(function () {
    Route::post('/race', ['uses' => 'RaceController@store', 'as' => 'race.store']);
    Route::get('/races/active', ['uses' => 'ActiveRaceController@index', 'as' => 'race.active.index']);
    Route::get('/races/finished/{raceLength}', ['uses' => 'FinishedRaceController@index', 'as' => 'race.finished.index']);
});

Route::namespace('\App\Performance\Http\Controller')->group(function () {
    Route::get('/performances/fastest/{raceLength}', ['uses' => 'FastestPerformancesController@index', 'as' => 'performances.fastest.index']);
});
