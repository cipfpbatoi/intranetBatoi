<?php

Route::get('/allProgramacion/', ['as' => 'modulogrupo.all', 'uses' => 'PanelModuloGrupoController@index']);
Route::get('/modulogrupo/{modulogrupo}/pdf', ['as' => 'modulogrupo.programacion', 'uses' =>  'PanelModuloGrupoController@pdf']);

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

Route::post('/signatura/{id}/upload', ['as' => 'signatura.upload', 'uses' => 'SignaturaController@upload']);
Route::get('/A3', ['as' => 'signaturaAlumno.index', 'uses' => 'SignaturaAlumneController@index']);
Route::get('/signatura/{id}/pdf', ['as' => 'signatura.pdf', 'uses' => 'SignaturaController@pdf']);
