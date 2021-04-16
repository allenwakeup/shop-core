<?php

Route::get ('/schedules', 'ScheduleController@index')->name ('schedule.index');
Route::get ('/schedules/list', 'ScheduleController@list')->name ('schedule.list');
Route::get ('/schedules/create', 'ScheduleController@create')->name ('schedule.create');
Route::post ('/schedules', 'ScheduleController@save')->name ('schedule.save');
Route::get ('/schedules/{id}/detail', 'ScheduleController@detail')->name ('schedule.detail');
Route::get ('/schedules/{id}/edit', 'ScheduleController@edit')->name ('schedule.edit');
Route::put ('/schedules/{id}', 'ScheduleController@update')->name ('schedule.update');
Route::delete ('/schedules/{id}', 'ScheduleController@delete')->name ('schedule.delete');
