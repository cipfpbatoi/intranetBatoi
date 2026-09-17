<?php

Route::get('/home', ['as' => 'home.alumno', 'uses' => 'Auth\Alumno\HomeController@index']);
Route::get('/logout', ['as' => 'logout.alumno', 'uses' => 'Auth\Alumno\LoginController@logout']);
Route::get('/legal', ['as' => 'alumno.legal', 'uses' => 'Auth\Alumno\HomeController@legal']);

Route::get('/perfil', ['as' => 'perfilalumno.edit', 'uses' => 'Auth\Alumno\PerfilController@editar']);
Route::put('/perfil', ['as' => 'perfilalumno.update', 'uses' => 'Auth\Alumno\PerfilController@update']);

Route::get('/curso', ['as' => 'curso.direccion.index', 'uses' => 'PanelAlumnoCursoController@index']);
Route::get('/alumnocurso/{curso}/register', ['as' => 'alumnocurso.register', 'uses' => 'AlumnoCursoController@register']);
Route::get('/alumnocurso/{curso}/unregister', ['as' => 'alumnocurso.unregister', 'uses' => 'AlumnoCursoController@unregister']);
Route::get('/equipo', ['as' => 'alumno.equipo', 'uses' => 'AlumnoController@equipo']);
Route::get('/empresas',['as' => 'alumno.empresa', 'uses' => 'ColaboracionAlumnoController@index']);

Route::get('/A3',['as' => 'signatura.alumne','uses' => 'SignaturaAlumneController@index']);
Route::post('/A3/upload',['as' => 'signatura.alumne.upload','uses' => 'SignaturaAlumneController@uploadPost']);

Route::post('/profesor/{profesor}/mensaje', ['as' => 'alumno.mensaje', 'uses' => 'AlumnoController@alerta']);

Route::prefix('convalidacions')->name('convalidacions.')->group(function () {
    Route::get('/', ['as' => 'index', 'uses' => 'ConvalidacioController@index']);
    Route::get('/create', ['as' => 'create', 'uses' => 'ConvalidacioController@create']);
    Route::post('/upload-certificat', ['as' => 'upload-certificat', 'uses' => 'ConvalidacioController@uploadCertificat']);
    Route::post('/', ['as' => 'store', 'uses' => 'ConvalidacioController@store']);
    Route::get('/{sollicitud}', ['as' => 'show', 'uses' => 'ConvalidacioController@show']);
    Route::get('/{convalidacio}/download', ['as' => 'download', 'uses' => 'ConvalidacioController@download']);
});

