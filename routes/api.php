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

Route::resource('alumnoFct', 'AlumnoFctController', ['except' => ['edit', 'create']]);
Route::get('alumnoFct/{grupo}/grupo','AlumnoFctController@indice');
Route::get('/convenio','EmpresaController@indexConvenio');
Route::get('miIp','IPController@miIP');
Route::get('actividad/{actividad}/getFiles','ActividadController@getFiles');




Route::group(['middleware' => 'auth:api'], function() {
    Route::resource('dual', 'DualController', ['except' => ['edit', 'create']]);
    Route::get('misAlumnosFct','AlumnoFctController@misAlumnos');
    Route::resource('actividad', 'ActividadController', ['except' => ['edit', 'create']]);
    Route::resource('programacion', 'ProgramacionController', ['except' => ['edit', 'create']]);
    Route::resource('reunion', 'ReunionController', ['except' => ['edit', 'create']]);
    Route::resource('falta', 'FaltaController', ['except' => ['edit', 'create']]);
    Route::resource('documento', 'DocumentoController', ['except' => ['edit', 'create']]);
    Route::resource('modulo_ciclo', 'Modulo_cicloController', ['except' => ['edit', 'create']]);
    Route::resource('resultado', 'ResultadoController', ['except' => ['edit', 'create']]);
    Route::resource('comision', 'ComisionController', ['except' => ['edit', 'create']]);
    Route::resource('instructor', 'InstructorController', ['except' => ['edit', 'create']]);
    Route::get('autorizar/comision', 'ComisionController@autorizar');
    Route::get('notification/{id}', 'NotificationController@leer');
    Route::resource('ppoll', 'PPollController', ['except' => ['edit', 'create']]);

    Route::resource('profesor', 'ProfesorController', ['except' => ['edit', 'create']]);
    Route::get('profesor/{dni}/rol', 'ProfesorController@rol');
    Route::get('profesor/rol/{rol}','ProfesorController@getRol');
    Route::get('ficha', 'ProfesorController@ficha');
    Route::get('doficha', 'FicharController@fichar');
    Route::get('ipGuardia','FicharController@ip');
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
    Route::get('inventario','MaterialController@inventario');

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
    Route::resource('tipoincidencia', 'TipoIncidenciaController', ['except' => ['edit', 'create']]);
    Route::resource('expediente', 'ExpedienteController', ['except' => ['edit', 'create']]);
    Route::resource('tipoExpediente', 'TipoExpedienteController', ['except' => ['edit', 'create']]);    
    Route::resource('alumnogrupo', 'AlumnoGrupoController', ['except' => ['edit', 'create']]);
    Route::resource('activity', 'ActivityController', ['except' => ['edit', 'create']]);
    Route::resource('curso', 'CursoController', ['except' => ['edit', 'create']]);
    Route::get('alumnoGrupoModulo/{dni}/{modulo}','AlumnoGrupoController@getModulo');
    
    Route::resource('horario', 'HorarioController', ['except' => ['edit', 'create']]);
    Route::get('horario/{idProfesor}/guardia','HorarioController@Guardia');
    Route::get('horariosDia/{fecha}','HorarioController@HorariosDia');
    Route::resource('hora', 'HoraController', ['except' => ['edit', 'create']]);
    Route::put('/asistencia/cambiar', 'AsistenciaController@cambiar');
    Route::put('/reunion/{idReunion}/alumno/{idAlumno}','ReunionController@putAlumno');

    Route::get('/tiporeunion/{id}', 'TipoReunionController@show');
    Route::get('/modulo/{id}', 'ModuloController@show');
    Route::get('/ciclo/{id}', 'CicloController@show');
    
    Route::get('horarioChange/{dni}','HorarioController@getChange');
    Route::post('horarioChange/{dni}','HorarioController@Change');
   
    Route::post('/centro/fusionar','CentroController@fusionar');
    Route::get('colaboracion/instructores/{id}','ColaboracionController@instructores');
    Route::get('/colaboracion/{colaboracion}/resolve','ColaboracionController@resolve');
    Route::get('/colaboracion/{colaboracion}/refuse','ColaboracionController@refuse');
    Route::get('/colaboracion/{colaboracion}/unauthorize','ColaboracionController@unauthorize');
    Route::get('/colaboracion/{colaboracion}/switch','ColaboracionController@switch');
    Route::post('/colaboracion/{colaboracion}/telefonico', 'ColaboracionController@telefon');

    Route::get('/documentacionFCT/{documento}','DocumentacionFCTController@exec');

    Route::resource('alumnoresultado','AlumnoResultadoContoller');
    Route::get('/matricula/{token}','AlumnoReunionController@getDadesMatricula');
    Route::get('/test/matricula/{token}','AlumnoReunionController@getTestMatricula');
    Route::post('/alumno/{dni}/foto','AlumnoController@putImage');

    Route::resource('lote','LoteController',['except' => ['edit', 'create']]);
    Route::get('lote/{id}/articulos','LoteController@getArticulos');
    Route::put('lote/{id}/articulos','LoteController@putArticulos');
    Route::resource('articuloLote','ArticuloLoteController');
    Route::resource('articulo','ArticuloController');
    Route::get('articuloLote/{id}/materiales','ArticuloLoteController@getMateriales');

    Route::post('attachFile','DropZoneController@attachFile');
    Route::get('getAttached/{modelo}/{id}','DropZoneController@getAttached');
    Route::get('removeAttached/{modelo}/{id}/{file}','DropZoneController@removeAttached');

    Route::get('activity/{id}/move/{fct}','ActivityController@move');

});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
});