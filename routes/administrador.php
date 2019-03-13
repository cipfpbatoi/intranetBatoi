<?php

Route::resource('/menu', 'MenuController', ['except' => ['destroy', 'update']]);
Route::get('/menu/{menu}/delete', ['as' => 'menu.destroy', 'uses' => 'MenuController@destroy']);
Route::get('/menu/{menu}/active', ['as' => 'menu.active', 'uses' => 'MenuController@active']);
Route::post('/menu/create', ['as' => 'menu.store', 'uses' => 'MenuController@store']);
//manteniment menu
Route::put('/menu/{menu}/edit', ['as' => 'menu.update', 'uses' => 'MenuController@update']);
Route::get('/menu/{menu}/copy', ['as' => 'menu.copy', 'uses' => 'MenuController@copy']);
Route::get('/menu/{menu}/up', ['as' => 'menu.up', 'uses' => 'MenuController@up']);
Route::get('/menu/{menu}/down', ['as' => 'menu.down', 'uses' => 'MenuController@down']);

//importació
Route::get('/import', ['as' => 'import.create', 'uses' => 'ImportController@create']);
Route::post('/import', ['as' => 'import.store', 'uses' => 'ImportController@store']);
Route::get('/teacherImport', ['as' => 'teacherImport.create', 'uses' => 'TeacherImportController@create']);
Route::post('/teacherImport', ['as' => 'teacherImport.store', 'uses' => 'TeacherImportController@store']);

//manteniment taula mòduls
Route::resource('/modulo', 'ModuloController', ['except' => ['destroy', 'update', 'show']]);
Route::put('/modulo/{modulo}/edit', ['as' => 'modulo.update', 'uses' => 'ModuloController@update']);
Route::get('/modulo/asigna', ['as' => 'modulo.asigna', 'uses' => 'ModuloController@asigna']);

//enviament d'apitokens massiu
Route::get('/apiToken', ['as' => 'profesor.apiToken', 'uses' => 'AdministracionController@allApiToken']);

//manteniment taula cicle
Route::resource('/ciclo', 'CicloController', ['except' => ['destroy', 'update', 'show']]);
Route::put('/ciclo/{ciclo}/edit', ['as' => 'ciclo.update', 'uses' => 'CicloController@update']);
Route::get('/ciclo/{ciclo}/delete', ['as' => 'ciclo.destroy', 'uses' => 'CicloController@destroy']);

//canvia d'usuari
Route::get('/profesor/{idProfesor}/change',['as' =>'profesor.change','uses' => 'ProfesorController@change']);

// esborra taules canvi de curs
Route::get('/nuevoCurso',['as' => 'curso.nuevo.index', 'uses' => 'AdministracionController@nuevoCursoIndex']);
Route::post('/nuevoCurso',['as' => 'curso.nuevo', 'uses' => 'AdministracionController@nuevoCurso']);

// esborra programacions obsoletes
Route::get('/programacion/deleteOld',['as' => 'programacion.deleteCall', 'uses' => 'AdministracionController@deleteProgramacionIndex']);
Route::post('/programacion/deleteOld',['as' => 'programacion.deleteOld', 'uses' => 'AdministracionController@deleteProgramacion']);

// modificar funcions en l'horari
Route::resource('/horario', 'HorarioController', ['except' => ['destroy', 'update','create']]);
Route::get('/horario/{profesor}/cambiar',['uses'=>'HorarioController@modificarHorario']);
Route::put('/horario/{horario}/edit', ['as' => 'horario.update', 'uses' => 'HorarioController@update']);

// manteniment taula modul_cicle
Route::resource('/modulo_ciclo', 'Modulo_cicloController', ['except' => ['destroy', 'update', 'show']]);
Route::post('/modulo_ciclo/create', ['as' => 'moduloCiclo.store', 'uses' => 'Modulo_cicloController@store']);
Route::put('/modulo_ciclo/{ciclo}/edit', ['as' => 'moduloCiclo.update', 'uses' => 'Modulo_cicloController@update']);
Route::get('/modulo_ciclo/{ciclo}/delete', ['as' => 'moduloCiclo.destroy', 'uses' => 'Modulo_cicloController@destroy']);

// manteniment
Route::get('/actualizacion','ActualizacionController@actualizacion');
Route::get('/anexoI','AdministracionController@importaAnexoI');
//Route::get('/tutores','ImportController@asignarTutores');