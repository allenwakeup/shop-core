<?php

Route::get ('/dataMaps', 'DataMapController@index')->name ('dataMap.index');
Route::get ('/dataMaps/list', 'DataMapController@list')->name ('dataMap.list');
Route::get ('/dataMaps/create', 'DataMapController@create')->name ('dataMap.create');
Route::post ('/dataMaps', 'DataMapController@save')->name ('dataMap.save');
Route::get ('/dataMaps/{id}/detail', 'DataMapController@detail')->name ('dataMap.detail');
Route::get ('/dataMaps/{id}/edit', 'DataMapController@edit')->name ('dataMap.edit');
Route::post('/dataMaps/{id}/assignment/{left_id}/save', 'DataMapController@saveAssignment')->name('dataMap.assignment.save');
Route::delete('/dataMaps/{id}/assignment/{left_id}/delete', 'DataMapController@deleteAssignment')->name('dataMap.assignment.delete');
Route::get('/dataMaps/{id}/assignment/select/{left_id}', 'DataMapController@selectAssignment')->name('dataMap.assignment.select');
Route::get('/dataMaps/{id}/assignment', 'DataMapController@assignment')->name('dataMap.assignment');
Route::put ('/dataMaps/{id}', 'DataMapController@update')->name ('dataMap.update');
Route::delete ('/dataMaps/{id}', 'DataMapController@delete')->name ('dataMap.delete');
