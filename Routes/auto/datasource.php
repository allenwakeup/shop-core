<?php

Route::get ('/datasources', 'DatasourceController@index')->name ('datasource.index');
Route::get ('/datasources/list', 'DatasourceController@list')->name ('datasource.list');
Route::get ('/datasources/create', 'DatasourceController@create')->name ('datasource.create');
Route::post ('/datasources', 'DatasourceController@save')->name ('datasource.save');
Route::get ('/datasources/{id}/edit', 'DatasourceController@edit')->name ('datasource.edit');
Route::put ('/datasources/{id}', 'DatasourceController@update')->name ('datasource.update');
Route::delete ('/datasources/{id}', 'DatasourceController@delete')->name ('datasource.delete');
