<?php

Route::get('/areas', 'AreaController@index')->name('area.index');
Route::get('/areas/list', 'AreaController@list')->name('area.list');
Route::get('/areas/create', 'AreaController@create')->name('area.create');
Route::post('/areas', 'AreaController@save')->name('area.save');
Route::get('/areas/{id}/edit', 'AreaController@edit')->name('area.edit');
Route::put('/areas/{id}', 'AreaController@update')->name('area.update');
Route::delete('/areas/{id}', 'AreaController@delete')->name('area.delete');
