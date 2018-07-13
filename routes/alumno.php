<?php

Route::get('/home', ['as' => 'home.alumno', 'uses' => 'Auth\Alumno\HomeController@index']);
Route::get('/logout', ['as' => 'logout.alumno', 'uses' => 'Auth\Alumno\LoginController@logout']);
Route::get('/legal', ['as' => 'legal', 'uses' => 'Auth\Alumno\HomeController@legal']);

Route::get('/perfil', ['as' => 'perfilalumno.edit', 'uses' => 'Auth\Alumno\PerfilController@editar']);
Route::put('/perfil', ['as' => 'perfilalumno.update', 'uses' => 'Auth\Alumno\PerfilController@update']);

Route::get('/curso', ['as' => 'curso.direccion.index', 'uses' => 'PanelAlumnoCursoController@index']);
Route::get('/alumnocurso/{curso}/register', ['as' => 'alumnocurso.register', 'uses' => 'AlumnoCursoController@register']);
Route::get('/alumnocurso/{curso}/unregister', ['as' => 'alumnocurso.unregister', 'uses' => 'AlumnoCursoController@unregister']);
Route::get('/equipo', ['as' => 'alumno.equipo', 'uses' => 'AlumnoController@equipo']);

Route::post('/profesor/{profesor}/mensaje', ['as' => 'alumno.mensaje', 'uses' => 'AlumnoController@alerta']);
