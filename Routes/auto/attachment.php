<?php

Route::get('/attachments', 'AttachmentController@index')->name('attachment.index');
Route::get('/attachments/list', 'AttachmentController@list')->name('attachment.list');
Route::get('/attachments/create', 'AttachmentController@create')->name('attachment.create');
Route::post('/attachments', 'AttachmentController@save')->name('attachment.save');
Route::get('/attachments/{attachable_id}/{attachable}/{id}', 'AttachmentController@download')->name('attachment.download');
Route::put('/attachments/{id}', 'AttachmentController@update')->name('attachment.update');
Route::delete('/attachments/{id}', 'AttachmentController@delete')->name('attachment.delete');
