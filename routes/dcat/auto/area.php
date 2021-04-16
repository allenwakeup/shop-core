<?php
Route::get('/areas', 'AreaController@index');
Route::get('/areas/create', 'AreaController@form');
Route::get('/areas/{id}/edit', 'AreaController@edit');
Route::get('/areas/{id}', 'AreaController@show');
Route::post('/areas', 'AreaController@store');
Route::put('/areas/{id}', 'AreaController@update');
Route::delete('/areas/{id}', 'AreaController@destroy');
