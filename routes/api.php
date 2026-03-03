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

Route::resource('alumnofct', 'AlumnoFctController', ['except' => [ 'create']])->middleware('auth:sanctum');
Route::resource('projecte', 'ProjecteController', ['except' => [ 'create']]);
Route::get('alumnofct/{grupo}/grupo', 'AlumnoFctController@indice')->middleware('auth:sanctum');
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
Route::resource('actividad', 'ActividadController', ['except' => [ 'create']])->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('grupo/list/{id}', 'GrupoController@list')->middleware('auth:sanctum');
    Route::get('alumnofct/{grupo}/dual', 'AlumnoFctController@dual')->middleware('auth:sanctum');
    Route::get('fct/{id}/alFct', 'FctController@llist')->middleware('auth:sanctum');
    Route::post('fct/{id}/alFct', 'FctController@seguimiento')->middleware('auth:sanctum');
    Route::resource('programacion', 'ProgramacionController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('reunion', 'ReunionController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('falta', 'FaltaController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('documento', 'DocumentoController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('modulo_ciclo', 'Modulo_cicloController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('resultado', 'ResultadoController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('comision', 'ComisionController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::put('/comision/{dni}/prePay', 'ComisionController@prePay')->middleware('auth:sanctum');
    Route::resource('instructor', 'InstructorController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('ipguardia', 'IpGuardiaController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('setting', 'SettingController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('autorizar/comision', 'ComisionController@autorizar')->middleware('auth:sanctum');
    Route::get('notification/{id}', 'NotificationController@leer')->middleware('auth:sanctum');
    Route::resource('ppoll', 'PPollController', ['except' => [ 'create']])->middleware('auth:sanctum');

    Route::resource('profesor', 'ProfesorController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('profesor/{dni}/rol', 'ProfesorController@rol')->middleware('auth:sanctum');
    Route::get('profesor/rol/{rol}', 'ProfesorController@getRol')->middleware('auth:sanctum');
    Route::get('ipGuardias', 'IpGuardiaController@arrayIps')->middleware('auth:sanctum');


    Route::resource('faltaProfesor', 'FaltaProfesorController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('faltaProfesor/horas/{condicion}', 'FaltaProfesorController@horas')->middleware('auth:sanctum');

    Route::put('/material/cambiarUbicacion/', 'MaterialController@putUbicacion')->middleware('auth:sanctum');
    Route::put('/material/cambiarEstado/', 'MaterialController@putEstado')->middleware('auth:sanctum');
    Route::put('/material/cambiarUnidad/', 'MaterialController@putUnidades')->middleware('auth:sanctum');
    Route::put('/material/cambiarInventario', 'MaterialController@putInventario')->middleware('auth:sanctum');
    Route::get('/material/espacio/{espacio}', 'MaterialController@getMaterial')->middleware('auth:sanctum');
    Route::resource('material', 'MaterialController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('inventario', 'MaterialController@inventario')->middleware('auth:sanctum');
    Route::get('inventario/{espai}', 'MaterialController@espai')->middleware('auth:sanctum');
    Route::resource('materialbaja', 'MaterialBajaController', ['except' => [ 'create']])->middleware('auth:sanctum');

    Route::resource('espacio', 'EspacioController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('guardia/range', 'GuardiaController@range')->middleware('auth:sanctum');
    Route::resource('guardia', 'GuardiaController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('departamento', 'DepartamentoController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('reserva', 'ReservaController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('ordenreunion', 'OrdenReunionController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('colaboracion', 'ColaboracionController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('centro', 'CentroController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('grupotrabajo', 'GrupoTrabajoController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('Empresa', 'EmpresaController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('ordentrabajo', 'OrdenTrabajoController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('incidencia', 'IncidenciaController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('tipoincidencia', 'TipoIncidenciaController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('expediente', 'ExpedienteController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('solicitud', 'SolicitudController', ['except' => ['create']])->middleware('auth:sanctum');
    Route::resource('tipoExpediente', 'TipoExpedienteController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('alumnogrupo', 'AlumnoGrupoController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('activity', 'ActivityController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('curso', 'CursoController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('ciclo', 'CicloController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('task', 'TaskController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('alumnoGrupoModulo/{dni}/{modulo}', 'AlumnoGrupoController@getModulo')->middleware('auth:sanctum');
    
    Route::resource('horario', 'HorarioController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('horario/{idProfesor}/guardia', 'HorarioController@Guardia')->middleware('auth:sanctum');
    Route::get('horariosDia/{fecha}', 'HorarioController@HorariosDia')->middleware('auth:sanctum');
    Route::resource('hora', 'HoraController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::put('/asistencia/cambiar', 'AsistenciaController@cambiar');
    Route::put('/reunion/{idReunion}/alumno/{idAlumno}', 'ReunionController@putAlumno')->middleware('auth:sanctum');

    Route::get('/tiporeunion/{id}', 'TipoReunionController@show')->middleware('auth:sanctum');
    Route::get('/modulo/{id}', 'ModuloController@show')->middleware('auth:sanctum');
    
    Route::get('horarioChange/{dni}', 'HorarioController@getChange')->middleware('auth:sanctum');
    Route::post('horarioChange/{dni}', 'HorarioController@Change')->middleware('auth:sanctum');
   
    Route::post('/centro/fusionar', 'CentroController@fusionar')->middleware('auth:sanctum');
    Route::get('colaboracion/instructores/{id}', 'ColaboracionController@instructores')->middleware('auth:sanctum');
    Route::get('/colaboracion/{colaboracion}/resolve', 'ColaboracionController@resolve')->middleware('auth:sanctum');
    Route::get('/colaboracion/{colaboracion}/refuse', 'ColaboracionController@refuse')->middleware('auth:sanctum');
    Route::get('/colaboracion/{colaboracion}/unauthorize', 'ColaboracionController@unauthorize')->middleware('auth:sanctum');
    Route::get('/colaboracion/{colaboracion}/switch', 'ColaboracionController@switch')->middleware('auth:sanctum');
    Route::post('/colaboracion/{colaboracion}/telefonico', 'ColaboracionController@telefon')->middleware('auth:sanctum');
    Route::post('/colaboracion/{colaboracion}/book', 'ColaboracionController@book')->middleware('auth:sanctum');

    Route::get('/documentacionFCT/{documento}', 'DocumentacionFCTController@exec')->middleware('auth:sanctum');
    Route::get('/signatura', 'DocumentacionFCTController@signatura')->middleware('auth:sanctum');
    Route::get('/signatura/director', 'DocumentacionFCTController@signaturaDirector')->middleware('auth:sanctum');
    Route::get('/signatura/a1', 'DocumentacionFCTController@signaturaA1')->middleware('auth:sanctum');

    Route::resource('alumnoresultado', 'AlumnoResultadoContoller', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('/matricula/{token}', 'AlumnoReunionController@getDadesMatricula')->middleware('auth:sanctum');
    Route::get('/test/matricula/{token}', 'AlumnoReunionController@getTestMatricula')->middleware('auth:sanctum');
    Route::post('/alumno/{dni}/foto', 'AlumnoController@putImage')->middleware('auth:sanctum');
    Route::post('/alumno/{dni}/dades', 'AlumnoController@putDades')->middleware('auth:sanctum');
    Route::post('/matricula/send', 'AlumnoReunionController@sendMatricula')->middleware('auth:sanctum');

    Route::resource('lote', 'LoteController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('lote/{id}/articulos', 'LoteController@getArticulos')->middleware('auth:sanctum');
    Route::put('lote/{id}/articulos', 'LoteController@putArticulos')->middleware('auth:sanctum');
    Route::resource('articuloLote', 'ArticuloLoteController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('articulo', 'ArticuloController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::get('articuloLote/{id}/materiales', 'ArticuloLoteController@getMateriales')->middleware('auth:sanctum');

    Route::resource('cotxe', 'CotxeController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::resource('tipoactividad', 'TipoActividadController', ['except' => [ 'create']])->middleware('auth:sanctum');
    Route::post('attachFile', 'DropZoneController@attachFile')->middleware('auth:sanctum');
    Route::get('getAttached/{modelo}/{id}', 'DropZoneController@getAttached')->middleware('auth:sanctum');
    Route::get('getNameAttached/{modelo}/{id}/{filename}', 'DropZoneController@getNameAttached')->middleware('auth:sanctum');
    Route::get('removeAttached/{modelo}/{id}/{file}', 'DropZoneController@removeAttached')->middleware('auth:sanctum');

    Route::get('activity/{id}/move/{fct}', 'ActivityController@move')->middleware('auth:sanctum');
    Route::get('tutoriagrupo/{id}','TutoriaGrupoController@show')->middleware('auth:sanctum');


   
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
