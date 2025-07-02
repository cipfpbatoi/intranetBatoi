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

Route::resource('alumnofct', 'AlumnoFctController', ['except' => [ 'create']]);
Route::resource('projecte', 'ProjecteController', ['except' => [ 'create']]);
Route::get('alumnofct/{grupo}/grupo', 'AlumnoFctController@indice');
Route::get('/convenio', 'EmpresaController@indexConvenio');
Route::get('miIp', 'IPController@miIP');
Route::get('actividad/{actividad}/getFiles', 'ActividadController@getFiles');
Route::get('server-time', 'GuardiaController@getServerTime' );
Route::post('eventPorta', 'CotxeController@eventPorta');



Route::group(['middleware' => 'auth:api'], function () {
    Route::get('grupo/list/{id}', 'GrupoController@list');
    Route::get('alumnofct/{grupo}/dual', 'AlumnoFctController@dual');
    Route::resource('dual', 'DualController', ['except' => [ 'create']]);
    Route::get('fct/{id}/alFct', 'FctController@llist');
    Route::post('fct/{id}/alFct', 'FctController@seguimiento');
    Route::get('misAlumnosFct', 'AlumnoFctController@misAlumnos');
    Route::resource('actividad', 'ActividadController', ['except' => [ 'create']]);
    Route::resource('programacion', 'ProgramacionController', ['except' => [ 'create']]);
    Route::resource('reunion', 'ReunionController', ['except' => [ 'create']]);
    Route::resource('falta', 'FaltaController', ['except' => [ 'create']]);
    Route::resource('documento', 'DocumentoController', ['except' => [ 'create']]);
    Route::resource('modulo_ciclo', 'Modulo_cicloController', ['except' => [ 'create']]);
    Route::resource('resultado', 'ResultadoController', ['except' => [ 'create']]);
    Route::resource('comision', 'ComisionController', ['except' => [ 'create']]);
    Route::put('/comision/{dni}/prePay', 'ComisionController@prePay');
    Route::resource('instructor', 'InstructorController', ['except' => [ 'create']]);
    Route::resource('ipguardia', 'IpGuardiaController');
    Route::resource('setting', 'SettingController');
    Route::get('autorizar/comision', 'ComisionController@autorizar');
    Route::get('notification/{id}', 'NotificationController@leer');
    Route::resource('ppoll', 'PPollController', ['except' => [ 'create']]);

    Route::resource('profesor', 'ProfesorController', ['except' => [ 'create']]);
    Route::get('profesor/{dni}/rol', 'ProfesorController@rol');
    Route::get('profesor/rol/{rol}', 'ProfesorController@getRol');
    Route::get('ficha', 'ProfesorController@ficha');
    Route::get('doficha', 'FicharController@fichar');
    Route::get('ipGuardias', 'IpGuardiaController@arrayIps');
    Route::get('verficha', 'FicharController@entrefechas');
    Route::get('itaca/{dia}/{idProfesor}', 'FaltaItacaController@potencial');
    Route::post('itaca', 'FaltaItacaController@guarda');
    Route::get('/aula', 'ReservaController@unsecure');


    Route::resource('faltaProfesor', 'FaltaProfesorController', ['except' => [ 'create']]);
    Route::get('faltaProfesor/horas/{condicion}', 'FaltaProfesorController@horas');

    Route::put('/material/cambiarUbicacion/', 'MaterialController@putUbicacion');
    Route::put('/material/cambiarEstado/', 'MaterialController@putEstado');
    Route::put('/material/cambiarUnidad/', 'MaterialController@putUnidades');
    Route::put('/material/cambiarInventario', 'MaterialController@putInventario');
    Route::get('/material/espacio/{espacio}', 'MaterialController@getMaterial');
    Route::resource('material', 'MaterialController', ['except' => [ 'create']]);
    Route::get('inventario', 'MaterialController@inventario');
    Route::get('inventario/{espai}', 'MaterialController@espai');
    Route::resource('materialbaja', 'MaterialBajaController', ['except' => [ 'create']]);

    Route::resource('espacio', 'EspacioController', ['except' => [ 'create']]);
    Route::resource('guardia', 'GuardiaController');
    Route::resource('departamento', 'DepartamentoController');
    Route::resource('reserva', 'ReservaController');
    Route::resource('ordenreunion', 'OrdenReunionController', ['except' => [ 'create']]);
    Route::resource('colaboracion', 'ColaboracionController', ['except' => [ 'create']]);
    Route::resource('centro', 'CentroController', ['except' => [ 'create']]);
    Route::resource('grupotrabajo', 'GrupoTrabajoController', ['except' => [ 'create']]);
    Route::resource('Empresa', 'EmpresaController', ['except' => [ 'create']]);
    Route::resource('ordentrabajo', 'OrdenTrabajoController', ['except' => [ 'create']]);
    Route::resource('incidencia', 'IncidenciaController', ['except' => [ 'create']]);
    Route::resource('tipoincidencia', 'TipoIncidenciaController', ['except' => [ 'create']]);
    Route::resource('expediente', 'ExpedienteController', ['except' => [ 'create']]);
    Route::resource('solicitud', 'SolicitudController', ['except' => ['create']]);
    Route::resource('tipoExpediente', 'TipoExpedienteController', ['except' => [ 'create']]);
    Route::resource('alumnogrupo', 'AlumnoGrupoController', ['except' => [ 'create']]);
    Route::resource('activity', 'ActivityController', ['except' => [ 'create']]);
    Route::resource('curso', 'CursoController', ['except' => [ 'create']]);
    Route::resource('ciclo', 'CicloController', ['except' => [ 'create']]);
    Route::resource('task', 'TaskController', ['except' => [ 'create']]);
    Route::get('alumnoGrupoModulo/{dni}/{modulo}', 'AlumnoGrupoController@getModulo');
    
    Route::resource('horario', 'HorarioController', ['except' => [ 'create']]);
    Route::get('horario/{idProfesor}/guardia', 'HorarioController@Guardia');
    Route::get('horariosDia/{fecha}', 'HorarioController@HorariosDia');
    Route::resource('hora', 'HoraController', ['except' => [ 'create']]);
    Route::put('/asistencia/cambiar', 'AsistenciaController@cambiar');
    Route::put('/reunion/{idReunion}/alumno/{idAlumno}', 'ReunionController@putAlumno');

    Route::get('/tiporeunion/{id}', 'TipoReunionController@show');
    Route::get('/modulo/{id}', 'ModuloController@show');
    
    Route::get('horarioChange/{dni}', 'HorarioController@getChange');
    Route::post('horarioChange/{dni}', 'HorarioController@Change');
   
    Route::post('/centro/fusionar', 'CentroController@fusionar');
    Route::get('colaboracion/instructores/{id}', 'ColaboracionController@instructores');
    Route::get('/colaboracion/{colaboracion}/resolve', 'ColaboracionController@resolve');
    Route::get('/colaboracion/{colaboracion}/refuse', 'ColaboracionController@refuse');
    Route::get('/colaboracion/{colaboracion}/unauthorize', 'ColaboracionController@unauthorize');
    Route::get('/colaboracion/{colaboracion}/switch', 'ColaboracionController@switch');
    Route::post('/colaboracion/{colaboracion}/telefonico', 'ColaboracionController@telefon');
    Route::post('/colaboracion/{colaboracion}/book', 'ColaboracionController@book');

    Route::get('/documentacionFCT/{documento}', 'DocumentacionFCTController@exec');
    Route::get('/signatura', 'DocumentacionFCTController@signatura');
    Route::get('/signatura/director', 'DocumentacionFCTController@signaturaDirector');
    Route::get('/signatura/a1', 'DocumentacionFCTController@signaturaA1');
    Route::get('/signatura/{id}','SignaturaController@show');

    Route::resource('alumnoresultado', 'AlumnoResultadoContoller');
    Route::get('/matricula/{token}', 'AlumnoReunionController@getDadesMatricula');
    Route::get('/test/matricula/{token}', 'AlumnoReunionController@getTestMatricula');
    Route::post('/alumno/{dni}/foto', 'AlumnoController@putImage');
    Route::post('/alumno/{dni}/dades', 'AlumnoController@putDades');
    Route::post('/matricula/send', 'AlumnoReunionController@sendMatricula');

    Route::resource('lote', 'LoteController', ['except' => [ 'create']]);
    Route::get('lote/{id}/articulos', 'LoteController@getArticulos');
    Route::put('lote/{id}/articulos', 'LoteController@putArticulos');
    Route::resource('articuloLote', 'ArticuloLoteController');
    Route::resource('articulo', 'ArticuloController');
    Route::get('articuloLote/{id}/materiales', 'ArticuloLoteController@getMateriales');

    Route::resource('cotxe', 'CotxeController');
    Route::post('attachFile', 'DropZoneController@attachFile');
    Route::get('getAttached/{modelo}/{id}', 'DropZoneController@getAttached');
    Route::get('getNameAttached/{modelo}/{id}/{filename}', 'DropZoneController@getNameAttached');
    Route::get('removeAttached/{modelo}/{id}/{file}', 'DropZoneController@removeAttached');

    Route::get('activity/{id}/move/{fct}', 'ActivityController@move');
    Route::get('tutoriagrupo/{id}','TutoriaGrupoController@show');



});

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
});
