<?php

Route::get ('/databases', 'DatabaseController@index')->name ('database.index');
Route::get ('/databases/list', 'DatabaseController@list')->name ('database.list');