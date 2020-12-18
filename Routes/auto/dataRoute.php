<?php

Route::get ('/dataRoutes', 'DataRouteController@index')->name ('dataRoute.index');
Route::get ('/dataRoutes/list', 'DataRouteController@list')->name ('dataRoute.list');
Route::get ('/dataRoutes/create', 'DataRouteController@create')->name ('dataRoute.create');
Route::post ('/dataRoutes', 'DataRouteController@save')->name ('dataRoute.save');
Route::get ('/dataRoutes/{id}/detail', 'DataRouteController@detail')->name ('dataRoute.detail');
Route::get ('/dataRoutes/{id}/edit', 'DataRouteController@edit')->name ('dataRoute.edit');
Route::put ('/dataRoutes/{id}', 'DataRouteController@update')->name ('dataRoute.update');
Route::delete ('/dataRoutes/{id}', 'DataRouteController@delete')->name ('dataRoute.delete');
