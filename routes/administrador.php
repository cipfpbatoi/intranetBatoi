<?php

Route::resource('/menu', 'MenuController', ['except' => ['destroy', 'update']]);
Route::get('/menu/{menu}/delete', ['as' => 'menu.destroy', 'uses' => 'MenuController@destroy']);
Route::get('/menu/{menu}/active', ['as' => 'menu.active', 'uses' => 'MenuController@active']);
Route::post('/menu/create', ['as' => 'menu.store', 'uses' => 'MenuController@store']);
Route::put('/menu/{menu}/edit', ['as' => 'menu.update', 'uses' => 'MenuController@update']);
Route::get('/menu/{menu}/copy', ['as' => 'menu.copy', 'uses' => 'MenuController@copy']);
Route::get('/menu/{menu}/up', ['as' => 'menu.up', 'uses' => 'MenuController@up']);
Route::get('/menu/{menu}/down', ['as' => 'menu.down', 'uses' => 'MenuController@down']);

Route::get('/import', ['as' => 'import.create', 'uses' => 'ImportController@create']);
Route::post('/import', ['as' => 'import.store', 'uses' => 'ImportController@store']);

Route::resource('/modulo', 'ModuloController', ['except' => ['destroy', 'update', 'show']]);
Route::put('/modulo/{modulo}/edit', ['as' => 'modulo.update', 'uses' => 'ModuloController@update']);
Route::get('/modulo/asigna', ['as' => 'modulo.asigna', 'uses' => 'ModuloController@asigna']);
Route::get('/apiToken', ['as' => 'profesor.apiToken', 'uses' => 'ProfesorController@allApiToken']);

Route::resource('/ciclo', 'CicloController', ['except' => ['destroy', 'update', 'show']]);
Route::put('/ciclo/{ciclo}/edit', ['as' => 'ciclo.update', 'uses' => 'CicloController@update']);
Route::get('/ciclo/{ciclo}/delete', ['as' => 'ciclo.destroy', 'uses' => 'CicloController@destroy']);
Route::get('/profesor/{idProfesor}/change',['as' =>'profesor.change','uses' => 'ProfesorController@change']);

Route::get('/fcts', ['as' => 'fct.admin.index', 'uses' => 'PanelFctController@index']);
Route::get('/instructor/ini',['as' => 'instructor.ini','uses'=>'InstructorController@load']);
//Route::get('/tmp/dia',['as' => 'tmp','uses'=>'DocumentoController@tmpInstructores']);
Route::get('/nuevoCurso',['as' => 'curso.nuevo.index', 'uses' => 'AdministracionController@nuevoCursoIndex']);
Route::post('/nuevoCurso',['as' => 'curso.nuevo', 'uses' => 'AdministracionController@nuevoCurso']);

Route::get('/programacion/deleteOld',['as' => 'programacion.deleteCall', 'uses' => 'AdministracionController@deleteProgramacionIndex']);
Route::post('/programacion/deleteOld',['as' => 'programacion.deleteOld', 'uses' => 'AdministracionController@deleteProgramacion']);


//Route::resource('/horario', 'HorarioController', ['except' => ['destroy', 'update']]);
//Route::get('/horario/{horario}/delete', ['as' => 'horario.destroy', 'uses' => 'HorarioController@destroy']);
//Route::post('/horario/create', ['as' => 'horario.store', 'uses' => 'HorarioController@store']);
//Route::put('/horario/{horario}/edit', ['as' => 'horario.update', 'uses' => 'HorarioController@update']);