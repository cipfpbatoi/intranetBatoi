<?php

use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */
//Route::resource('profesor', 'ProfesorController', ['except' => ['edit', 'create']]);
Route::get('/convenio','EmpresaController@indexConvenio'); 
Route::group(['middleware' => 'auth:api'], function() {
    Route::resource('actividad', 'ActividadController', ['except' => ['edit', 'create']]);
    Route::resource('programacion', 'ProgramacionController', ['except' => ['edit', 'create']]);
    Route::resource('reunion', 'ReunionController', ['except' => ['edit', 'create']]);
    Route::resource('falta', 'FaltaController', ['except' => ['edit', 'create']]);
    Route::resource('resultado', 'ResultadoController', ['except' => ['edit', 'create']]);
    Route::resource('comision', 'ComisionController', ['except' => ['edit', 'create']]);
    Route::resource('instructor', 'InstructorController', ['except' => ['edit', 'create']]);
    Route::get('autorizar/comision', 'ComisionController@autorizar');
    Route::get('notification/{id}', 'NotificationController@leer');

    Route::resource('profesor', 'ProfesorController', ['except' => ['edit', 'create']]);
    Route::get('profesor/{dni}/rol', 'ProfesorController@rol');
//    Route::resource('fichar','FicharController',['except' => ['edit', 'create']]);
    Route::get('ficha', 'ProfesorController@ficha');
    Route::get('doficha', 'FicharController@fichar');
    //Route::get('fichar', 'FicharController@miraficha');
    Route::get('verficha', 'FicharController@entrefechas');
    Route::get('itaca/{dia}/{idProfesor}','FaltaItacaController@potencial');
    Route::post('itaca','FaltaItacaController@guarda');
    
    Route::resource('faltaProfesor', 'FaltaProfesorController', ['except' => ['edit', 'create']]);
    Route::get('faltaProfesor/horas/{condicion}','FaltaProfesorController@horas');
    
    Route::put('/material/cambiarUbicacion/', 'MaterialController@putUbicacion');
    Route::put('/material/cambiarEstado/', 'MaterialController@putEstado');
    Route::put('/material/cambiarUnidad/', 'MaterialController@putUnidades');
    Route::put('/material/cambiarInventario', 'MaterialController@putInventario');
    Route::get('/material/espacio/{espacio}', 'MaterialController@getMaterial');
    Route::resource('material', 'MaterialController', ['except' => ['edit', 'create']]);

    Route::resource('espacio', 'EspacioController', ['except' => ['edit', 'create']]);
    Route::resource('guardia', 'GuardiaController');
    Route::resource('reserva', 'ReservaController');
    Route::resource('ordenreunion', 'OrdenReunionController', ['except' => ['edit', 'create']]);
    Route::resource('colaboracion', 'ColaboracionController', ['except' => ['edit', 'create']]);
    Route::resource('centro', 'CentroController', ['except' => ['edit', 'create']]);
    Route::resource('GrupoTrabajo', 'GrupoTrabajoController', ['except' => ['edit', 'create']]);
    Route::resource('Empresa', 'EmpresaController', ['except' => ['edit', 'create']]);
    Route::resource('ordentrabajo', 'OrdenTrabajoController', ['except' => ['edit', 'create']]);
    Route::resource('incidencia', 'IncidenciaController', ['except' => ['edit', 'create']]);
    Route::resource('expediente', 'ExpedienteController', ['except' => ['edit', 'create']]);

    Route::resource('horario', 'HorarioController', ['except' => ['edit', 'create']]);
    Route::get('horariosDia/{fecha}','HorarioController@HorariosDia');
    Route::resource('hora', 'HoraController', ['except' => ['edit', 'create']]);
//Route::resource('asistencia','AsistenciaController',['except'=>['edit','create']]);
    Route::put('/asistencia/cambiar', 'AsistenciaController@cambiar');



    Route::get('/tiporeunion/{id}', 'TipoReunionController@show');
    Route::get('/modulo/{id}', 'ModuloController@show');
    Route::get('/ciclo/{id}', 'CicloController@show');
    
    Route::get('horarioChange/{dni}','HorarioController@getChange');
    Route::post('horarioChange/{dni}','HorarioController@Change');
   
    Route::post('/centro/fusionar','CentroController@fusionar');
    Route::get('colaboracion/instructores/{id}','ColaboracionController@instructores');
    //Route::get('/convenio','EmpresaController@indexConvenio');
});
