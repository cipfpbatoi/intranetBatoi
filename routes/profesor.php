<?php


Route::get('/home', ['as' => 'home.profesor', 'uses' => 'Auth\Profesor\HomeController@index']);
Route::get('/legal', ['as' => 'legal', 'uses' => 'Auth\Profesor\HomeController@legal']);
Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\Profesor\LoginController@logout']);



Route::get('/perfil', ['as' => 'perfil.edit', 'uses' => 'Auth\Profesor\PerfilController@editar']);
Route::put('/perfil', ['as' => 'perfil.update', 'uses' => 'Auth\Profesor\PerfilController@update']);


Route::resource('/actividad', 'ActividadController', ['except' => ['show', 'destroy', 'update']]);
Route::get('/actividad/{actividad}/delete', ['as' => 'actividad.destroy', 'uses' => 'ActividadController@destroy']);
Route::get('/actividad/listado', ['as' => 'actividad.listado', 'uses' => 'ActividadController@listado']);
Route::post('/actividad/create', ['as' => 'actividad.store', 'uses' => 'ActividadController@store']);
Route::put('/actividad/{actividad}/edit', ['as' => 'actividad.update', 'uses' => 'ActividadController@update']);
Route::get('/actividad/{actividad}/detalle', ['as' => 'actividad.detalle', 'uses' => 'ActividadController@detalle']);
Route::get('/actividad/{actividad}/gestor', ['as' => 'actividad.gestor', 'uses' => 'ActividadController@gestor']);
Route::post('/actividad/{actividad}/nuevoGrupo', 'ActividadController@altaGrupo');
Route::get('/actividad/{actividad}/borrarGrupo/{grupo}', 'ActividadController@borrarGrupo');
Route::post('/actividad/{actividad}/nuevoProfesor', 'ActividadController@altaProfesor');
Route::get('/actividad/{actividad}/borrarProfesor/{profesor}', 'ActividadController@borrarProfesor');
Route::get('/actividad/{actividad}/coordinador/{profesor}', 'ActividadController@Coordinador');
Route::get('actividad/campo/{campo}', 'ActividadController@includegrid');
Route::get('/actividad/{actividad}/init', ['as' => 'actividad.init', 'uses' => 'ActividadController@init']);
Route::get('/actividad/{actividad}/show', ['as' => 'actividad.show', 'uses' => 'ActividadController@show']);
Route::get('/actividad/{actividad}/notification', ['as' => 'actividad.notificar', 'uses' => 'ActividadController@notify']);
Route::get('/actividad/{actividad}/autorizacion', ['as' => 'actividad.autorizacion', 'uses' => 'ActividadController@autorizacion']);
Route::get('/actividad/{actividad}/ics', ['as' => 'actividad.ics', 'uses' => 'ActividadController@i_c_s']);
Route::get('/actividadOrientacion', ['as' => 'actividad.orientacion', 'uses' => 'PanelActividadOrientacionController@index']);
Route::get('/actividadorientacion/create', ['as' => 'actividad.createOrientacion', 'uses' => 'PanelActividadOrientacionController@create']);
Route::post('/actividadorientacion/create', ['as' => 'actividad.storeOrientacion', 'uses' => 'ActividadController@store']);



Route::resource('/reunion', 'ReunionController', ['except' => ['destroy', 'update']]);
Route::get('/reunion/{reunion}/delete', ['as' => 'reunion.destroy', 'uses' => 'ReunionController@destroy']);
Route::post('/reunion/create', ['as' => 'reunion.store', 'uses' => 'ReunionController@store']);
Route::put('/reunion/{reunion}/edit', ['as' => 'reunion.update', 'uses' => 'ReunionController@update']);
Route::post('/reunion/{reunion}/nuevoOrden', 'ReunionController@altaOrden');
Route::get('/reunion/{reunion}/borrarOrden/{orden}', 'ReunionController@borrarOrden');
Route::post('/reunion/{reunion}/nuevoProfesor', 'ReunionController@altaProfesor');
Route::get('/reunion/{reunion}/borrarProfesor/{profesor}', 'ReunionController@borrarProfesor');
Route::get('/reunion/{reunion}/coordinador/{profesor}', 'ReunionController@Coordinador');
Route::get('reunion/campo/{campo}', 'ReunionController@includegrid');
Route::get('/reunion/{reunion}/email', ['as' => 'reunion.email', 'uses' => 'ReunionController@email']);
Route::get('/reunion/{reunion}/pdf', ['as' => 'reunion.pdf', 'uses' => 'ReunionController@pdf']);
Route::get('/reunion/{reunion}/show', ['as' => 'reunion.show', 'uses' => 'ReunionController@show']);
Route::get('/reunion/{reunion}/notification', ['as' => 'reunion.notificar', 'uses' => 'ReunionController@notify']);
Route::get('/reunion/{reunion}/autorizacion', ['as' => 'reunion.autorizacion', 'uses' => 'ReunionController@autorizacion']);
Route::get('/ordenreunion/update', ['as' => 'ordenreunion.update', 'uses' => 'ReunionController@oupdate']);
Route::get('/reunion/{reunion}/saveFile', ['as' => 'reunion.saveFile', 'uses' => 'ReunionController@saveFile']);
Route::get('/reunion/{reunion}/ics', ['as' => 'reunion.ics', 'uses' => 'ReunionController@ics']);

Route::resource('/grupotrabajo', 'GrupoTrabajoController', ['except' => ['destroy', 'update']]);
Route::get('/grupotrabajo/{grupotrabajo}/delete', ['as' => 'grupotrabajo.destroy', 'uses' => 'GrupoTrabajoController@destroy']);
Route::post('/grupotrabajo/create', ['as' => 'grupotrabajo.store', 'uses' => 'GrupoTrabajoController@store']);
Route::put('/grupotrabajo/{grupotrabajo}/edit', ['as' => 'grupotrabajo.update', 'uses' => 'GrupoTrabajoController@update']);
Route::get('/grupotrabajo/{grupotrabajo}/detalle', ['as' => 'grupotrabajo.detalle', 'uses' => 'GrupoTrabajoController@detalle']);
Route::post('/grupotrabajo/{grupotrabajo}/nuevoProfesor', 'GrupoTrabajoController@altaProfesor');
Route::get('/grupotrabajo/{grupotrabajo}/borrarProfesor/{profesor}', 'GrupoTrabajoController@borrarProfesor');
Route::get('/grupotrabajo/{grupotrabajo}/coordinador/{profesor}', 'GrupoTrabajoController@Coordinador');

Route::resource('/curso', 'CursoController', ['except' => ['destroy', 'update']]);
Route::get('/curso/{curso}/delete', ['as' => 'curso.destroy', 'uses' => 'CursoController@destroy']);
Route::post('/curso/create', ['as' => 'curso.store', 'uses' => 'CursoController@store']);
Route::put('/curso/{curso}/edit', ['as' => 'comision.update', 'uses' => 'CursoController@update']);
Route::get('/curso/{curso}/detalle', ['as' => 'curso.detalle', 'uses' => 'CursoController@detalle']);
Route::get('/alumnocurso/{grupo}', ['as' => 'alumnocurso.show', 'uses' => 'AlumnoCursoController@indice']);
Route::get('/alumnocurso/{id}/delete', ['as' => 'alumnocurso.delete', 'uses' => 'AlumnoCursoController@destroy']);
Route::get('/alumnocurso/{id}/active', ['as' => 'alumnocurso.active', 'uses' => 'AlumnoCursoController@active']);
Route::get('/curso/{curso}/active', ['as' => 'curso.active', 'uses' => 'CursoController@active']);
Route::get('/curso/{curso}/pdf', ['as' => 'curso.pdf', 'uses' => 'CursoController@pdf']);
Route::get('/curso/{curso}/saveFile', ['as' => 'curso.save', 'uses' => 'CursoController@saveFile']);
Route::get('/alumnocurso/{alumno}/registerAlumno/{curso}', ['as' => 'Alumnocurso.registerA', 'uses' => 'AlumnoCursoController@registerAlumn']);
Route::get('/alumnocurso/{grupo}/registerGrupo/{curso}', ['as' => 'Alumnocurso.registerG', 'uses' => 'AlumnoCursoController@registerGrup']);
Route::get('/alumnocurso/{id}/pdf', ['as' => 'Alumnocurso.pdf', 'uses' => 'AlumnoCursoController@pdf']);


Route::resource('/comision', 'ComisionController', ['except' => ['destroy', 'update']]);
Route::get('/comision/{comision}/delete', ['as' => 'comision.destroy', 'uses' => 'ComisionController@destroy']);
Route::post('/comision/create', ['as' => 'comision.store', 'uses' => 'ComisionController@store']);
Route::put('/comision/{comision}/edit', ['as' => 'comision.update', 'uses' => 'ComisionController@update']);
Route::get('/comision/{comision}/cancel', ['as' => 'comision.cancel', 'uses' => 'ComisionController@cancel']);
Route::get('/comision/{comision}/show', ['as' => 'comision.show', 'uses' => 'ComisionController@show']);
Route::get('/comision/{comision}/pdf', ['as' => 'comision.pdf', 'uses' => 'ComisionController@pdf']);
Route::get('/comision/{comision}/init', ['as' => 'comision.init', 'uses' => 'ComisionController@init']);
Route::get('/comision/{comision}/notification', ['as' => 'comision.notificar', 'uses' => 'ComisionController@notify']);
Route::get('/comision/{comision}/unpaid', ['as' => 'comision.unpaid', 'uses' => 'ComisionController@unpaid']);


Route::get('/grupo', ['as' => 'grupo.index', 'uses' => 'GrupoController@index']);
Route::get('/grupo/{grupo}/detalle', ['as' => 'grupo.detalle', 'uses' => 'GrupoController@detalle']);
Route::get('/grupo/{grupo}/pdf', ['as' => 'grupo.pdf', 'uses' => 'GrupoController@pdf']);
Route::get('/grupo/{grupo}/edit', ['as' => 'grupo.edit', 'uses' => 'GrupoController@edit']);
Route::put('/grupo/{grupo}/edit', ['as' => 'grupo.update', 'uses' => 'GrupoController@update']);
Route::get('/grupo/{grupo}/carnet', ['as' => 'grupo.carnet', 'uses' => 'GrupoController@carnet']);
Route::get('/grupo/{grupo}/fse', ['as' => 'grupo.fse', 'uses' => 'GrupoController@fse']);
Route::get('/alumno_grupo/{grupo}/show', ['as' => 'alumnogrupo.index', 'uses' => 'AlumnoGrupoController@indice']);
Route::get('/alumno_grupo/{grupo}/profile', ['as' => 'alumnogrupo.profile', 'uses' => 'AlumnoGrupoController@profile']);
Route::get('/grupo/{grupo}/horario', ['as' => 'grupo.horario', 'uses' => 'GrupoController@horario']);
Route::get('/grupo/asigna', ['as' => 'grupo.asigna', 'uses' => 'GrupoController@asigna']);


Route::get('/alumno/{alumno}/muestra', ['as' => 'alumno.show', 'uses' => 'AlumnoController@show']);
Route::get('/alumno/{alumno}/edit', ['as' => 'alumno.edit', 'uses' => 'AlumnoController@edit']);
Route::put('/alumno/{alumno}/edit', ['as' => 'alumno.update', 'uses' => 'AlumnoController@update']);
Route::get('/alumno/{alumno}/carnet', ['as' => 'alumno.carnet', 'uses' => 'AlumnoController@carnet']);
Route::get('/alumno/{alumno}/baja', ['as' => 'alumno.baja', 'uses' => 'AlumnoController@baja']);

Route::get('/departamento', ['as' => 'departamento.index', 'uses' => 'ProfesorController@departamento']);
Route::get('/equipo/{grupo}/grupo', ['as' => 'equipo.grupo', 'uses' => 'ProfesorController@equipo']);
Route::get('/equipoDirectivo', ['as' => 'equipodirectivo.index', 'uses' => 'ProfesorController@equipoDirectivo']);
Route::get('/profesor/{profesor}/horario', ['as' => 'profesor.horario', 'uses' => 'ProfesorController@horario']);
Route::get('/profesor/{profesor}/horario-cambiar', ['as' => 'profesor.horario-cambiar', 'uses' => 'ProfesorController@horarioCambiar']);
Route::get('/profesor/{profesor}/muestra', ['as' => 'profesor.show', 'uses' => 'ProfesorController@show']);
Route::get('/profesor/{profesor}/edit', ['as' => 'profesor.edit', 'uses' => 'ProfesorController@edit']);
Route::put('/profesor/{profesor}/edit', ['as' => 'profesor.update', 'uses' => 'ProfesorController@update']);
Route::get('/profesor/{profesor}/carnet', ['as' => 'profesor.carnet', 'uses' => 'ProfesorController@carnet']);
Route::get('/profesor/{profesor}/tarjeta', ['as' => 'profesor.tarjeta', 'uses' => 'ProfesorController@tarjeta']);
Route::post('/profesor/{profesor}/mensaje', ['as' => 'profesor.mensaje', 'uses' => 'ProfesorController@alerta']);
Route::post('/profesor/colectivo', ['as' => 'profesor.colectivo', 'uses' => 'ProfesorController@avisaColectivo']);
Route::get('/horario/change',['as' => 'horario.change', 'uses' => 'HorarioController@horarioCambiar']);
Route::get('/profesor/{profesor}/horario-aceptar',['as' => 'horario.change', 'uses' => 'HorarioController@changeTable']);


Route::get('/ficha', ['as' => 'fichar.ficha', 'uses' => 'FicharController@ficha']);


Route::resource('/falta', 'FaltaController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/falta/{falta}/delete', ['as' => 'falta.destroy', 'uses' => 'FaltaController@destroy']);
Route::get('/falta/create/direccion',['as' => 'falta.create.direccion' , 'uses' => 'PanelFaltaController@create']);
Route::post('/falta/create/direccion', ['as' => 'falta.store.direccion', 'uses' => 'FaltaController@store']);
Route::post('/falta/create', ['as' => 'falta.store', 'uses' => 'FaltaController@store']);
Route::put('/falta/{falta}/edit', ['as' => 'falta.update', 'uses' => 'FaltaController@update']);
Route::get('/falta/{falta}/pdf', ['as' => 'falta.pdf', 'uses' => 'FaltaController@pdf']);
Route::get('/falta/{falta}/init', ['as' => 'falta.init', 'uses' => 'FaltaController@init']);
Route::get('/falta/{falta}/notification', ['as' => 'falta.notificar', 'uses' => 'FaltaController@notify']);
Route::get('/falta/{falta}/show', ['as' => 'falta.show', 'uses' => 'FaltaController@show']);
Route::get('/falta/{falta}/document', ['as' => 'falta.document', 'uses' => 'FaltaController@document']);

Route::resource('/expediente', 'ExpedienteController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/expediente/{expediente}/delete', ['as' => 'expediente.destroy', 'uses' => 'ExpedienteController@destroy']);
Route::post('/expediente/create', ['as' => 'expediente.store', 'uses' => 'ExpedienteController@store']);
Route::put('/expediente/{expediente}/edit', ['as' => 'expediente.update', 'uses' => 'ExpedienteController@update']);
Route::get('/expediente/{expediente}/pdf', ['as' => 'expediente.pdf', 'uses' => 'ExpedienteController@pdf']);
Route::get('/expediente/{expediente}/notification', ['as' => 'expediente.notificar', 'uses' => 'ExpedienteController@notify']);
Route::get('/expediente/{expediente}/show', ['as' => 'expediente.show', 'uses' => 'ExpedienteController@show']);
Route::get('/expediente/{expediente}/init', ['as' => 'expediente.init', 'uses' => 'ExpedienteController@init']);
Route::get('/expediente/{expediente}/active', ['as' => 'expediente.active', 'uses' => 'ExpedienteController@pasaOrientacion']);
Route::get('/expedienteO', ['as' => 'expediente.orientacion', 'uses' => 'PanelExpedienteOrientacionController@index']);


Route::resource('/resultado', 'ResultadoController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/resultado/{resultado}/delete', ['as' => 'resultado.destroy', 'uses' => 'ResultadoController@destroy']);
Route::post('/resultado/create', ['as' => 'resultado.store', 'uses' => 'ResultadoController@store']);
Route::put('/resultado/{resultado}/edit', ['as' => 'resultado.update', 'uses' => 'ResultadoController@update']);
Route::get('/resultado/pdf', ['as' => 'resultado.pdf', 'uses' => 'ResultadoController@listado']);


//Empreses
Route::resource('/empresa', 'EmpresaController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/empresa/{empresa}/delete', ['as' => 'empresa.destroy', 'uses' => 'EmpresaController@destroy']);
Route::post('/empresa/create', ['as' => 'empresa.store', 'uses' => 'EmpresaController@guarda']);
Route::put('/empresa/{empresa}/edit', ['as' => 'empresa.update', 'uses' => 'EmpresaController@update']);
Route::get('/empresa/pdf', ['as' => 'empresa.pdf', 'uses' => 'EmpresaController@listado']);
Route::get('/empresa/{empresa}/detalle', ['as' => 'empresa.detalle', 'uses' => 'EmpresaController@show']);
Route::get('/empresa/{empresa}/document', ['as' => 'empresa.document', 'uses' => 'EmpresaController@document']);
Route::get('/colaboracion',['as' => 'colaboracion.index', 'uses' => 'ColaboracionController@index']);
Route::get('/empresaSC',['as'=>'empresaSC.index','uses'=>'PanelEmpresaSCController@index']);

Route::get('/misColaboraciones',['as' => 'colaboracion.mias', 'uses' => 'PanelColaboracionController@index']);
Route::get('/colaboracion/inicia', ['as' => 'PanelColaboracion.inicia', 'uses' => 'PanelColaboracionController@inicia']);
Route::get('/colaboracion/contacto', ['as' => 'PanelColaboracion.contacto', 'uses' => 'PanelColaboracionController@sendFirstContact']);
Route::get('/colaboracion/info', ['as' => 'PanelColaboracion.info', 'uses' => 'PanelColaboracionController@sendRequestInfo']);
Route::get('/colaboracion/documentacion', ['as' => 'PanelColaboracion.documentacion', 'uses' => 'PanelColaboracionController@sendDocumentation']);
Route::get('/colaboracion/student', ['as' => 'PanelColaboracion.student', 'uses' => 'PanelColaboracionController@sendStudent']);
Route::get('/colaboracion/seguimiento', ['as' => 'PanelColaboracion.seguimiento', 'uses' => 'PanelColaboracionController@follow']);
Route::get('/colaboracion/visita', ['as' => 'PanelColaboracion.visita', 'uses' => 'PanelColaboracionController@visit']);


Route::get('/colaboracion/{colaboracion}/documentacion', 'PanelColaboracionController@sendDocumentation');
Route::get('/colaboracion/{colaboracion}/contacto', 'PanelColaboracionController@sendFirstContact');
Route::get('/colaboracion/{colaboracion}/info', 'PanelColaboracionController@sendRequestInfo');
Route::get('/colaboracion/{colaboracion}/student', 'PanelColaboracionController@sendStudent');
Route::get('/colaboracion/{colaboracion}/seguimiento', 'PanelColaboracionController@follow');
Route::get('/colaboracion/{colaboracion}/visita', 'PanelColaboracionController@visit');

Route::resource('/centro', 'CentroController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/centro/{centro}/delete', ['as' => 'centro.destroy', 'uses' => 'CentroController@destroy']);
Route::put('/centro/{centro}/edit', ['as' => 'centro.update', 'uses' => 'CentroController@update']);
Route::post('/centro/create', ['as' => 'centro.store', 'uses' => 'CentroController@store']);
Route::get('/centro/{centro}/mapa', ['as' => 'centro.mapa', 'uses' => 'CentroController@mapa']);

Route::resource('/colaboracion', 'ColaboracionController', ['except' => ['destroy', 'update', 'show']]);
Route::post('/empresa/create', ['as' => 'empresa.store', 'uses' => 'EmpresaController@store']);
Route::get('/colaboracion/{colaboracion}/delete', ['as' => 'colaboracion.destroy', 'uses' => 'ColaboracionController@destroy']);
Route::get('/colaboracion/{colaboracion}/show', ['as' => 'colaboracion.show', 'uses' => 'ColaboracionController@show']);
Route::put('/colaboracion/{colaboracion}/edit', ['as' => 'colaboracion.update', 'uses' => 'ColaboracionController@update']);
Route::get('/colaboracion/{colaboracion}/copy', ['as' => 'colaboracion.copy', 'uses' => 'ColaboracionController@copy']);
Route::post('/colaboracion/create', ['as' => 'colaboracion.store', 'uses' => 'ColaboracionController@store']);

Route::resource('/fct', 'FctController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/fct/{id}/{alumno}/alumnoDelete',['as' => 'fct.alumno.delete', 'uses' => 'FctController@alumnoDelete']);
Route::post('/fct/{id}/alumnoCreate', ['as' => 'fct.alumno.create', 'uses' => 'FctController@nouAlumno']);
Route::post('/fct/{id}/instructorCreate',['as'=>'fct.instructor.create','uses'=>'FctController@nouInstructor']);
Route::get('/fct/{id}/{dni}/instructorDelete',['as'=>'fct.instructor.delete','uses'=>'FctController@deleteInstructor']);
Route::post('/fct/{id}/modificaHoras',['as'=>'fct.modificarHoras','uses'=>'FctController@modificaHoras']);
Route::get('/fct/{id}/delete', ['as' => 'fct.destroy', 'uses' => 'FctController@destroy']);
Route::get('/fct/{id}/show', ['as' => 'fct.show', 'uses' => 'FctController@show']);
Route::put('/fct/{id}/edit', ['as' => 'fct.update', 'uses' => 'FctController@update']);
Route::get('/fct/create/{colaboracio}', ['as' => 'fct.create2', 'uses' => 'FctController@create']);
Route::post('/fct/create', ['as' => 'fct.store', 'uses' => 'FctController@store']);
Route::post('/fct/pass',['as' => 'fct.pass', 'uses' => 'FctController@store']);
Route::post('/fct/{id}/pdf', ['as' => 'fct.pdf', 'uses' => 'FctController@pdf']);
Route::get('/fct/{document}/print', ['as' => 'fct.print', 'uses' => 'FctController@document']);
Route::post('/fct/{document}/print',['as'=>'fct.print.post','uses'=> 'FctController@documentPost']);

Route::get('/avalFct', ['as' => 'aval.fct', 'uses' => 'PanelFctAvalController@index']);
Route::get('/fct/{document}/apte', ['as' => 'fct.apte', 'uses' => 'PanelFctAvalController@apte']);
Route::get('/fct/{document}/noApte', ['as' => 'fct.noApte', 'uses' => 'PanelFctAvalController@noApte']);
Route::get('/fct/{document}/noAval', ['as' => 'fct.noAval', 'uses' => 'PanelFctAvalController@noAval']);
Route::get('/fct/{document}/noProyecto', ['as' => 'fct.noProyecto', 'uses' => 'PanelFctAvalController@noProyecto']);
Route::get('/fct/{document}/nuevoProyecto', ['as' => 'fct.nuevoProyecto', 'uses' => 'PanelFctAvalController@nuevoProyecto']);
Route::get('/fct/acta', ['as' => 'fct.acta', 'uses' => 'PanelFctAvalController@demanarActa']);
Route::get('/fct/{fct}/proyecto', ['as' => 'proyecto.new', 'uses' => 'DocumentoController@project']);
Route::post('/fct/{fct}/proyecto', ['as' => 'proyecto.create', 'uses' => 'DocumentoController@store']);
Route::get('/fct/upload', ['as' => 'qualitat.new', 'uses' => 'DocumentoController@qualitat']);
Route::post('/fct/upload', ['as' => 'qualitat.create', 'uses' => 'DocumentoController@store']);
Route::put('/fct/upload', ['as' => 'qualitat.update', 'uses' => 'DocumentoController@update']);
Route::get('/fct/{document}/empresa',['as' => 'fct.empresa', 'uses' => 'PanelFctAvalController@empresa']);
Route::get('/fct/{id}/modificaNota', ['as' => 'fct.editNota', 'uses' => 'PanelFctAvalController@edit']);
Route::put('/fct/{id}/modificaNota', ['as' => 'fct.updateNota', 'uses' => 'PanelFctAvalController@update']);

Route::resource('dual','DualAlumnoController',['except' => ['destroy', 'update', 'show','edit']]);
Route::get('/dual/{id}/edit', ['as' => 'dual.edit', 'uses' => 'DualController@edit']);
Route::put('/dual/{id}/edit', ['as' => 'dual.update', 'uses' => 'DualController@update']);
Route::get('/dual/create', ['as' => 'dual.create', 'uses' => 'DualController@create']);
Route::post('/dual/create', ['as' => 'dual.store', 'uses' => 'DualController@store']);
Route::get('/dual/{id}/pdf/{informe}',['as' => 'dual.pdf', 'uses' => 'DualAlumnoController@informe']);
Route::get('/dual/{id}/delete', ['as' => 'dual.destroy', 'uses' => 'DualAlumnoController@destroy']);
Route::get('/dual/anexeVI',['as'=>'dual.anexevi','uses'=>'DualController@printAnexeVI']);


Route::resource('/alumnofct', 'FctAlumnoController', ['except' => ['destroy', 'update', 'show']]);
Route::put('/alumnofct/{id}/edit', ['as' => 'alumnofct.update', 'uses' => 'FctAlumnoController@update']);
Route::get('/alumnofct/{id}/delete', ['as' => 'alumnofct.destroy', 'uses' => 'FctAlumnoController@destroy']);
Route::get('/alumnofct/convalidacion',['as' => 'alumnofct.convalidacion', 'uses' => 'FctAlumnoController@nuevaConvalidacion']);
Route::post('/alumnofct/convalidacion',['as' => 'alumnofct.convalidacion', 'uses' => 'FctAlumnoController@storeConvalidacion']);
Route::get('/alumnofct/{id}/pdf', ['as' => 'alumnofct.pdf', 'uses' => 'FctAlumnoController@pdf']);
Route::get('/alumnofct/{id}/email', ['as' => 'alumnofct.email', 'uses' => 'FctAlumnoController@email']);
Route::get('/alumnofct/{id}/show', ['as' => 'alumnofct.show', 'uses' => 'FctAlumnoController@show']);
Route::get('/alumnofct/{id}/pg0301', ['as' => 'alumnofct.pg0301', 'uses' => 'FctAlumnoController@pg0301']);


Route::resource('/instructor', 'InstructorController', ['except' => ['destroy','show']]);
Route::get('/instructor/{instructor}/show', ['as' => 'instructor.show', 'uses' => 'InstructorController@show']);
Route::put('instructor/{id}/edit',['as' => 'instructor.edita', 'uses' => 'InstructorController@update']);
Route::get('/instructor/{id}/delete', ['as' => 'instructor.destroy', 'uses' => 'InstructorController@destroy']);
Route::get('/instructor/{id}/edit/{centro}', ['as' => 'instructor.edita', 'uses' => 'InstructorController@edita']);
Route::put('/instructor/{id}/edit/{centro}', ['as' => 'instructor.update', 'uses' => 'InstructorController@guarda']);
Route::get('/instructor/{centro}/create', ['as' => 'instructor.create', 'uses' => 'InstructorController@crea']);
Route::post('/instructor/{centro}/create', ['as' => 'instructor.store', 'uses' => 'InstructorController@almacena']);
Route::get('/instructor/{id}/delete/{centro}', ['as' => 'instructor.destroy', 'uses' => 'InstructorController@delete']);
Route::get('/instructor/{id}/copy/{centro}', ['as' => 'instructor.editcopy', 'uses' => 'InstructorController@copy']);
Route::post('/instructor/{id}/copy/{centro}', ['as' => 'instructor.copy', 'uses' => 'InstructorController@toCopy']);
Route::get('/instructor/{instructor}/pdf', ['as' => 'instructor.pdf', 'uses' => 'InstructorController@pdf']);

//    Route::get('/fct/{alumno}/asigna', ['as' => 'fct.asigna', 'uses' => 'FctController@asigna']);
//    Route::post('/fct/{alumno}/asigna', ['as' => 'fct.store', 'uses' => 'FctController@store']);
//    Route::put('/fct/{alumno}/asigna', ['as' => 'fct.update', 'uses' => 'FctController@update']);
//    
//RUTAS MATERIALES
Route::resource('/material', 'MaterialController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/material/{material}/delete', ['as' => 'material.destroy', 'uses' => 'MaterialController@destroy']);
Route::post('/material/create', ['as' => 'material.store', 'uses' => 'MaterialController@store']);
Route::put('/material/{material}/edit', ['as' => 'material.update', 'uses' => 'MaterialController@update']);
Route::get('/material/{espacio}/detalle', ['as' => 'material.espacio', 'uses' => 'MaterialController@espacio']);
Route::get('/material/{material}/copy', ['as' => 'material.copy', 'uses' => 'MaterialController@copy']);
Route::get('/material/{material}/incidencia', ['as' => 'material.incidencia', 'uses' => 'MaterialController@incidencia']);

//RUTAS INCIDENCIAS
Route::resource('/incidencia', 'IncidenciaController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/incidencia/{incidencia}/show', ['as' => 'incidencia.show', 'uses' => 'IncidenciaController@show']);
Route::get('/incidencia/{incidencia}/delete', ['as' => 'incidencia.destroy', 'uses' => 'IncidenciaController@destroy']);
Route::post('/incidencia/create', ['as' => 'incidencia.store', 'uses' => 'IncidenciaController@store']);
Route::put('/incidencia/{incidencia}/edit', ['as' => 'incidencia.update', 'uses' => 'IncidenciaController@update']);
Route::get('/incidencia/{incidencia}/notification', ['as' => 'incidencia.notification', 'uses' => 'IncidenciaController@notify']);


//RUTAS ESPACIOS
Route::resource('/espacio', 'EspacioController', ['except' => ['destroy', 'update', 'show']]);
Route::get('/espacio/{espacio}/delete', ['as' => 'espacio.destroy', 'uses' => 'EspacioController@destroy']);
Route::get('/espacio/verMateriales/{espacio}', 'EspacioController@getMateriales');
Route::put('/espacio/{espacio}/edit', ['as' => 'espacio.store', 'uses' => 'EspacioController@update']);
Route::post('/espacio/create', ['as' => 'espacio.create', 'uses' => 'EspacioController@store']);

//RUTAS PROGRAMACIONES   
Route::get('/programacion/{programacion}/delete', ['as' => 'programacion.destroy', 'uses' => 'ProgramacionController@destroy']);
Route::put('/programacion/{programacion}/edit', ['as' => 'programacion.store', 'uses' => 'ProgramacionController@update']);
Route::post('/programacion/create', ['as' => 'programacion.create', 'uses' => 'ProgramacionController@store']);
Route::get('/programacion/{programacion}/init', ['as' => 'programacion.init', 'uses' => 'ProgramacionController@init']);
Route::post('/programacion/{programacion}/anexo', ['as' => 'programacion.storeanexo', 'uses' => 'ProgramacionController@storeanexo']);
Route::get('/programacion/{programacion}/deleteanexo', ['as' => 'programacion.deleteanexo', 'uses' => 'ProgramacionController@deleteanexo']);
Route::get('/programacion/{programacion}/email', ['as' => 'programacion.email', 'uses' => 'ProgramacionController@email']);

//RUTAS TUTORIAS
Route::resource('/tutoria', 'TutoriaController', ['except' => ['destroy', 'update']]);
Route::post('/tutoria/create', ['as' => 'tutoria.store', 'uses' => 'TutoriaController@store']);
Route::put('/tutoria/{menu}/edit', ['as' => 'tutoria.update', 'uses' => 'TutoriaController@update']);
Route::get('/tutoria/{menu}/document', ['as' => 'tutoria.document', 'uses' => 'TutoriaController@document']);
Route::get('/tutoria/{menu}/anexo', ['as' => 'tutoria.anexo', 'uses' => 'TutoriaController@anexo']);
Route::get('/tutoria/{menu}/detalle', ['as' => 'tutoria.detalle', 'uses' => 'TutoriaController@detalle']);
Route::get('/tutoria/{tutoria}/delete', ['as' => 'tutoria.destroy', 'uses' => 'TutoriaController@destroy']);

//RUTAS TUTORIAGRUPO
Route::get('/tutoriagrupo/create/{tutoria}/{grupo}', ['as' => 'tutoriagrupo.create', 'uses' => 'TutoriaGrupoController@createfrom']);
Route::get('/tutoriagrupo/{id}', ['as' => 'tutoriagrupo.edit', 'uses' => 'TutoriaGrupoController@edit']);
Route::post('/tutoriagrupo/create/{tutoria}/{grupo}', ['as' => 'tutoriagrupo.store', 'uses' => 'TutoriaGrupoController@store']);
Route::put('/tutoriagrupo/{id}', ['as' => 'tutoriagrupo.update', 'uses' => 'TutoriaGrupoController@update']);
Route::get('/tutoriagrupo/indice/{id}', ['as' => 'tutoriagrupo.indice', 'uses' => 'TutoriaGrupoController@indice']);

Route::get('/guardia', ['as' => 'guardia.index', 'uses' => 'GuardiaController@index']);
Route::get('/reserva', ['as' => 'reserva.index', 'uses' => 'ReservaController@index']);

//API_TOKEN
Route::get('/myApiToken', ['as' => 'profesor.miapiToken', 'uses' => 'ProfesorController@miApiToken']);
//Documentaci
Route::get('/readme', ['as' => 'documentacio.miapiToken', 'uses' => 'ProfesorController@readme']);

Route::get('/resultado/list', ['as' => 'resultado.list', 'uses' => 'PanelListadoEntregasController@index']);
Route::get('/infdepartamento/pdf/{reunion}', ['as' => 'reunion.pdf', 'uses' => 'PanelListadoEntregasController@pdf']);
Route::get('/infdepartamento/{resultado}/aviso', ['as' => 'resultado.avisaFalta', 'uses' => 'PanelListadoEntregasController@avisaFaltaEntrega']);
Route::get('/infdepartamento/avisa', ['as' => 'resultado.avisa', 'uses' => 'PanelListadoEntregasController@avisaTodos']);
Route::post('/infdepartamento/create', 
        ['as' => 'resultado.hazInforme', 'uses' => 'PanelListadoEntregasController@hazInformeTrimestral']);
Route::put('/infdepartamento/create', 
        ['as' => 'resultado.modificaInforme', 'uses' => 'PanelListadoEntregasController@modificaInformeTrimestral']);
//Programacion
Route::resource('/programacion', 'ProgramacionController', ['except' => ['destroy', 'update', 'show']]);
Route::put('/programacion/{programacion}/seguimiento', 
        ['as' => 'programacion.updateseguimiento', 'uses' => 'ProgramacionController@updateSeguimiento']);
Route::get('/programacion/{programacion}/seguimiento', ['as' => 'programacion.seguimiento', 'uses' => 'ProgramacionController@seguimiento']);
Route::get('/itaca',['as' => 'itaca.birret', 'uses' => 'FaltaItacaController@index']);


Route::get('/profesor/change',['as' =>'profesor.backChange','uses' => 'ProfesorController@backChange']);

//Jefa de practicas
Route::get('/fctcap/{grupo}/check', ['as' => 'fct.acta', 'uses' => 'PanelPG0301Controller@indice']);
Route::get('/controlFct',['as'=> 'controlFct.index','uses' => 'PanelPracticasController@index']);

//gestor documental
Route::get('/actividad/{actividad}/gestor', ['as' => 'actividad.gestor', 'uses' => 'ActividadController@gestor']);
Route::get('/expediente/{actividad}/gestor', ['as' => 'expediente.gestor', 'uses' => 'ExpedienteController@gestor']);
Route::get('/falta/{actividad}/gestor', ['as' => 'falta.gestor', 'uses' => 'FaltaController@gestor']);
Route::get('/comision/{actividad}/gestor', ['as' => 'comision.gestor', 'uses' => 'ComisionController@gestor']);
Route::get('/itaca/{actividad}/gestor', ['as' => 'itaca.gestor', 'uses' => 'FaltaItacaController@gestor']);

//control guadira
Route::get('/guardia/control', ['as' => 'guardia.control', 'uses' => 'PanelGuardiaController@index']);

//polls
Route::resource('/poll', 'PollController', ['except' => ['destroy', 'update','show']]);
Route::post('/poll/create', ['as' => 'poll.store', 'uses' => 'PollController@store']);
Route::put('/poll/{id}/edit', ['as' => 'poll.update', 'uses' => 'PollController@update']);
Route::get('/poll/{id}/slave', ['as' => 'poll.slave', 'uses' => 'PollController@show']);
Route::get('/poll/{id}/active', ['as' => 'poll.show', 'uses' => 'PollController@active']);

Route::resource('/option', 'OptionController', ['except' => ['destroy', 'update','show']]);
Route::post('/option/create', ['as' => 'option.store', 'uses' => 'OptionController@store']);
Route::get('/option/{id}/delete', ['as' => 'option.destroy', 'uses' => 'OptionController@destroy']);

Route::get('/poll/{id}/show', ['as' => 'poll.resultShow', 'uses' => 'PollController@lookAtMyVotes']);
Route::get('/poll/{id}/chart', ['as' => 'poll.result', 'uses' => 'PollController@lookAtAllVotes']);

Route::post('/myMail','MyMailController@send');