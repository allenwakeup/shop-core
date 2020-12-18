<?php

Route::get ('/connections', 'ConnectionController@index')->name ('connection.index');
Route::get ('/connections/list', 'ConnectionController@list')->name ('connection.list');
Route::get ('/connections/create', 'ConnectionController@create')->name ('connection.create');
Route::get ('/connections/{action}/test', 'ConnectionController@test')->name ('connection.test');
Route::post ('/connections', 'ConnectionController@save')->name ('connection.save');
Route::get ('/connections/{id}/detail', 'ConnectionController@detail')->name ('connection.detail');
Route::get ('/connections/{id}/edit', 'ConnectionController@edit')->name ('connection.edit');
Route::put ('/connections/{id}', 'ConnectionController@update')->name ('connection.update');
Route::delete ('/connections/{id}', 'ConnectionController@delete')->name ('connection.delete');
