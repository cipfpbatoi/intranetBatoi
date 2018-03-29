<?php

Route::get('/programacion', ['as' => 'programacion.direccion.index', 'uses' => 'PanelProgramacionController@index']);
Route::post('/programacion/{programacion}/checklist', ['as' => 'programacion.checklist', 'uses' => 'ProgramacionController@checkList']);
Route::get('/programacion/{programacion}/unauthorize', ['as' => 'programacion.unauthorize', 'uses' => 'ProgramacionController@resign']);
Route::get('/programacion/{programacion}/resolve', ['as' => 'programacion.resolve', 'uses' => 'ProgramacionController@resolve']);
Route::get('/programacion/list', ['as' => 'programacion.list', 'uses' => 'ModuloController@listado']);


