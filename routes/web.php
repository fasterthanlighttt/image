<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'prefix' => 'watermark',
], function() {
    Route::post('/', 'WatermarkController@store');
});

Route::group([
    'prefix' => 'image',
], function() {
    Route::get('/', 'ImageController@index');
    Route::post('/', 'ImageController@store');
});

