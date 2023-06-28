<?php

Route::get('/allProgramacion/', ['as' => 'programacion.all', 'uses' => 'PanelProgramacionAllController@index']);
//Route::get('/programaciones/', ['as' => 'programacion.all', 'uses' => 'PanelProgramacionesController@index']);

Route::get('/programacion/{programacion}/show', ['as' => 'programacion.show', 'uses' => 'ProgramacionController@show']);
Route::get('/programacion/{programacion}/anexo', ['as' => 'programacion.anexo', 'uses' => 'ProgramacionController@anexo']);
Route::get('/programacion/{programacion}/document', ['as' => 'programacion.document', 'uses' => 'ProgramacionController@document']);
Route::get('/programacion/{programacion}/link', ['as' => 'programacion.link', 'uses' => 'ProgramacionController@link']);
Route::get('/programacion/{programacion}/veranexo/{anexo}', ['as' => 'programacion.veranexo', 'uses' => 'ProgramacionController@veranexo']);

Route::get('/notification', ['as' => 'notificacion.index', 'uses' => 'NotificationController@index']);
Route::get('/notification/{notification}/show', ['as' => 'notification.show', 'uses' => 'NotificationController@show']);
Route::get('/notification/{notification}/read', ['as' => 'notificacion.read', 'uses' => 'NotificationController@read']);
Route::get('/notification/readAll', ['as' => 'notificacion.readAll', 'uses' => 'NotificationController@readAll']);
Route::get('/notification/{notification}/delete', ['as' => 'notification.delete', 'uses' => 'NotificationController@destroy']);
Route::get('/notification/deleteAll', ['as' => 'notificacion.deleteAll', 'uses' => 'NotificationController@deleteAll']);

Route::resource('/documento', 'DocumentoController', ['except' => ['destroy', 'update']]);
Route::get('/documento/{documento}/delete', ['as' => 'documento.destroy', 'uses' => 'DocumentoController@destroy']);
Route::post('/documento/create', ['as' => 'documento.store', 'uses' => 'DocumentoController@store']);
Route::put('/documento/{documento}/edit', ['as' => 'documento.update', 'uses' => 'DocumentoController@update']);
Route::get('/documento/{documento}/show', ['as' => 'documento.show', 'uses' => 'DocumentoController@show']);
Route::get('/documento/{grupo}/grupo', ['as' => 'documento.grupo', 'uses' => 'PanelDocAgrupadosController@index']);
Route::get('/documento/{grupo}/acta', ['as' => 'documento.acta', 'uses' => 'PanelActaController@index']);
Route::get('/proyecto', ['as' => 'documento.proyecto', 'uses' => 'PanelProyectoController@index']);
Route::post('/profesor/{profesor}/mensaje', ['as' => 'profesor.mensaje', 'uses' => 'ProfesorController@alerta']);

Route::get('/help/{fichero}/{enlace}', ['as' => 'help' ,'uses' => 'AdministracionController@help']);

Route::get('/poll/{id}/do', ['as' => 'enquesta', 'uses' => 'PollController@preparaEnquesta']);
Route::post('/poll/{id}/do', ['as' => 'enquesta.post', 'uses' => 'PollController@guardaEnquesta']);
Route::get('/doPoll', ['as' => 'enquesta.do', 'uses' => 'PanelPollResponseController@index']);
