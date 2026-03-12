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

Route::resource('alumnofct', 'AlumnoFctController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
Route::resource('projecte', 'ProjecteController', ['except' => [ 'create']]);
Route::get('alumnofct/{grupo}/grupo', 'AlumnoFctController@indice')->middleware('auth:api,sanctum');
Route::get('/convenio', 'EmpresaController@indexConvenio');
Route::get('miIp', 'IPController@miIP');
Route::get('actividad/{actividad}/getFiles', 'ActividadController@getFiles');
Route::get('server-time', 'GuardiaController@getServerTime' );
Route::get('porta/obrir', 'CotxeController@obrirTest');
Route::post('porta/obrir-automatica', 'CotxeController@obrirAutomatica');
Route::post('eventPortaSortida', 'CotxeController@eventSortida');
Route::post('eventPorta', 'CotxeController@eventEntrada');
Route::get('/presencia/resumen-rango',   'PresenciaResumenController@rango' );
Route::post('/auth/exchange', 'AuthTokenController@exchange');
Route::get('/auth/me', 'AuthTokenController@me')->middleware('auth:sanctum');
Route::post('/auth/logout', 'AuthTokenController@logout')->middleware('auth:sanctum');
Route::resource('actividad', 'ActividadController', ['except' => [ 'create']])->middleware('auth:api,sanctum');

Route::group(['middleware' => 'auth:api,sanctum'], function () {
    Route::get('grupo/list/{id}', 'GrupoController@list')->middleware('auth:api,sanctum');
    Route::get('alumnofct/{grupo}/dual', 'AlumnoFctController@dual')->middleware('auth:api,sanctum');
    Route::get('fct/{id}/alFct', 'FctController@llist')->middleware('auth:api,sanctum');
    Route::post('fct/{id}/alFct', 'FctController@seguimiento')->middleware('auth:api,sanctum');
    Route::resource('programacion', 'ProgramacionController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('reunion', 'ReunionController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('falta', 'FaltaController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('documento', 'DocumentoController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('modulo_ciclo', 'Modulo_cicloController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('resultado', 'ResultadoController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('comision', 'ComisionController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::put('/comision/{dni}/prePay', 'ComisionController@prePay')->middleware('auth:api,sanctum');
    Route::resource('instructor', 'InstructorController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('ipguardia', 'IpGuardiaController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('setting', 'SettingController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('autorizar/comision', 'ComisionController@autorizar')->middleware('auth:api,sanctum');
    Route::get('notification/{id}', 'NotificationController@leer')->middleware('auth:api,sanctum');
    Route::resource('ppoll', 'PPollController', ['except' => [ 'create']])->middleware('auth:api,sanctum');

    Route::resource('profesor', 'ProfesorController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('profesor/{dni}/rol', 'ProfesorController@rol')->middleware('auth:api,sanctum');
    Route::get('profesor/rol/{rol}', 'ProfesorController@getRol')->middleware('auth:api,sanctum');
    Route::get('ipGuardias', 'IpGuardiaController@arrayIps')->middleware('auth:api,sanctum');


    Route::resource('faltaProfesor', 'FaltaProfesorController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('faltaProfesor/horas/{condicion}', 'FaltaProfesorController@horas')->middleware('auth:api,sanctum');

    Route::put('/material/cambiarUbicacion/', 'MaterialController@putUbicacion')->middleware('auth:api,sanctum');
    Route::put('/material/cambiarEstado/', 'MaterialController@putEstado')->middleware('auth:api,sanctum');
    Route::put('/material/cambiarUnidad/', 'MaterialController@putUnidades')->middleware('auth:api,sanctum');
    Route::put('/material/cambiarInventario', 'MaterialController@putInventario')->middleware('auth:api,sanctum');
    Route::get('/material/espacio/{espacio}', 'MaterialController@getMaterial')->middleware('auth:api,sanctum');
    Route::resource('material', 'MaterialController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('inventario', 'MaterialController@inventario')->middleware('auth:api,sanctum');
    Route::get('inventario/{espai}', 'MaterialController@espai')->middleware('auth:api,sanctum');
    Route::resource('materialbaja', 'MaterialBajaController', ['except' => [ 'create']])->middleware('auth:api,sanctum');

    Route::resource('espacio', 'EspacioController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('guardia/range', 'GuardiaController@range')->middleware('auth:api,sanctum');
    Route::resource('guardia', 'GuardiaController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('departamento', 'DepartamentoController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('reserva', 'ReservaController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('ordenreunion', 'OrdenReunionController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('colaboracion', 'ColaboracionController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('centro', 'CentroController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('grupotrabajo', 'GrupoTrabajoController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('Empresa', 'EmpresaController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('ordentrabajo', 'OrdenTrabajoController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('incidencia', 'IncidenciaController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('tipoincidencia', 'TipoIncidenciaController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('expediente', 'ExpedienteController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('solicitud', 'SolicitudController', ['except' => ['create']])->middleware('auth:api,sanctum');
    Route::resource('tipoExpediente', 'TipoExpedienteController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('alumnogrupo', 'AlumnoGrupoController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('activity', 'ActivityController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('curso', 'CursoController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('ciclo', 'CicloController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('task', 'TaskController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('alumnoGrupoModulo/{dni}/{modulo}', 'AlumnoGrupoController@getModulo')->middleware('auth:api,sanctum');
    
    Route::resource('horario', 'HorarioController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('horario/{idProfesor}/guardia', 'HorarioController@Guardia')->middleware('auth:api,sanctum');
    Route::get('horariosDia/{fecha}', 'HorarioController@HorariosDia')->middleware('auth:api,sanctum');
    Route::resource('hora', 'HoraController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::put('/asistencia/cambiar', 'AsistenciaController@cambiar');
    Route::put('/reunion/{idReunion}/alumno/{idAlumno}', 'ReunionController@putAlumno')->middleware('auth:api,sanctum');

    Route::get('/tiporeunion/{id}', 'TipoReunionController@show')->middleware('auth:api,sanctum');
    Route::get('/modulo/{id}', 'ModuloController@show')->middleware('auth:api,sanctum');
    
    Route::get('horarioChange/{dni}', 'HorarioController@getChange')->middleware('auth:api,sanctum');
    Route::post('horarioChange/{dni}', 'HorarioController@Change')->middleware('auth:api,sanctum');
   
    Route::post('/centro/fusionar', 'CentroController@fusionar')->middleware('auth:api,sanctum');
    Route::get('colaboracion/instructores/{id}', 'ColaboracionController@instructores')->middleware('auth:api,sanctum');
    Route::get('/colaboracion/{colaboracion}/resolve', 'ColaboracionController@resolve')->middleware('auth:api,sanctum');
    Route::get('/colaboracion/{colaboracion}/refuse', 'ColaboracionController@refuse')->middleware('auth:api,sanctum');
    Route::get('/colaboracion/{colaboracion}/unauthorize', 'ColaboracionController@unauthorize')->middleware('auth:api,sanctum');
    Route::get('/colaboracion/{colaboracion}/switch', 'ColaboracionController@switch')->middleware('auth:api,sanctum');
    Route::post('/colaboracion/{colaboracion}/telefonico', 'ColaboracionController@telefon')->middleware('auth:api,sanctum');
    Route::post('/colaboracion/{colaboracion}/book', 'ColaboracionController@book')->middleware('auth:api,sanctum');

    Route::get('/documentacionFCT/{documento}', 'DocumentacionFCTController@exec')->middleware('auth:api,sanctum');
    Route::get('/signatura', 'DocumentacionFCTController@signatura')->middleware('auth:api,sanctum');
    Route::get('/signatura/director', 'DocumentacionFCTController@signaturaDirector')->middleware('auth:api,sanctum');
    Route::get('/signatura/a1', 'DocumentacionFCTController@signaturaA1')->middleware('auth:api,sanctum');

    Route::resource('alumnoresultado', 'AlumnoResultadoContoller', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('/matricula/{token}', 'AlumnoReunionController@getDadesMatricula')->middleware('auth:api,sanctum');
    Route::get('/test/matricula/{token}', 'AlumnoReunionController@getTestMatricula')->middleware('auth:api,sanctum');
    Route::post('/alumno/{dni}/foto', 'AlumnoController@putImage')->middleware('auth:api,sanctum');
    Route::post('/alumno/{dni}/dades', 'AlumnoController@putDades')->middleware('auth:api,sanctum');
    Route::post('/matricula/send', 'AlumnoReunionController@sendMatricula')->middleware('auth:api,sanctum');

    Route::resource('lote', 'LoteController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('lote/{id}/articulos', 'LoteController@getArticulos')->middleware('auth:api,sanctum');
    Route::put('lote/{id}/articulos', 'LoteController@putArticulos')->middleware('auth:api,sanctum');
    Route::resource('articuloLote', 'ArticuloLoteController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('articulo', 'ArticuloController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::get('articuloLote/{id}/materiales', 'ArticuloLoteController@getMateriales')->middleware('auth:api,sanctum');

    Route::resource('cotxe', 'CotxeController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::resource('tipoactividad', 'TipoActividadController', ['except' => [ 'create']])->middleware('auth:api,sanctum');
    Route::post('attachFile', 'DropZoneController@attachFile')->middleware('auth:api,sanctum');
    Route::get('getAttached/{modelo}/{id}', 'DropZoneController@getAttached')->middleware('auth:api,sanctum');
    Route::get('getNameAttached/{modelo}/{id}/{filename}', 'DropZoneController@getNameAttached')->middleware('auth:api,sanctum');
    Route::get('removeAttached/{modelo}/{id}/{file}', 'DropZoneController@removeAttached')->middleware('auth:api,sanctum');

    Route::get('activity/{id}/move/{fct}', 'ActivityController@move')->middleware('auth:api,sanctum');
    Route::get('tutoriagrupo/{id}','TutoriaGrupoController@show')->middleware('auth:api,sanctum');


   
});

Route::group(['middleware' => 'auth:api,sanctum'], function () {
    Route::get('doficha', 'FicharController@fichar');
    Route::get('verficha', 'FicharController@entrefechas');
    Route::get('itaca/{dia}/{idProfesor}', 'FaltaItacaController@potencial');
    Route::post('itaca', 'FaltaItacaController@guarda');
    Route::get('/aula', 'ReservaController@unsecure');
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
});
