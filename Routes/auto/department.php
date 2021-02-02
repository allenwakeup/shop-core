<?php

Route::get('/departments', 'DepartmentController@index')->name('department.index');
Route::get('/departments/list', 'DepartmentController@list')->name('department.list');
Route::get('/departments/tree', 'DepartmentController@tree')->name('department.tree');
Route::get('/departments/create', 'DepartmentController@create')->name('department.create');
Route::post('/departments', 'DepartmentController@save')->name('department.save');
Route::get('/departments/{id}/edit', 'DepartmentController@edit')->name('department.edit');
Route::put('/departments/{id}', 'DepartmentController@update')->name('department.update');
Route::delete ('/departments/{id}', 'DepartmentController@delete')->name ('department.delete');