<?php

Route::get('/profesor', ['as' => 'profesor.index', 'uses' => 'ProfesorController@index']);
Route::post('/profesor/{profesor}/mensaje', ['as' => 'direccion.mensaje', 'uses' => 'ProfesorController@alerta']);


Route::get('/comision', ['as' => 'comision.direccion.index', 'uses' => 'PanelComisionController@index']);
Route::get('/comision/{comision}/authorize', ['as' => 'comision.authorize', 'uses' => 'ComisionController@accept']);
Route::get('/comision/{comision}/unauthorize', ['as' => 'comision.unauthorize', 'uses' => 'ComisionController@resign']);
Route::get('/comision/{comision}/show', ['as' => 'comision.dir.show', 'uses' => 'ComisionController@show']);
Route::post('/comision/{comision}/refuse', ['as' => 'comision.refuse', 'uses' => 'ComisionController@refuse']);
Route::get('/comision/pdf', ['as' => 'comision.pdf', 'uses' => 'ComisionController@printAutoritzats']);
Route::get('/comision/autorizar', ['as' => 'comision.autorizar', 'uses' => 'ComisionController@autorizar']);
Route::get('/comision/paid', ['as' => 'comision.paid', 'uses' => 'ComisionController@payment']);

Route::get('/expediente', ['as' => 'expediente.direccion.index', 'uses' => 'PanelExpedienteController@index']);
Route::get('/expediente/{expediente}/authorize', ['as' => 'expediente.authorize', 'uses' => 'ExpedienteController@accept']);
Route::get('/expediente/{expediente}/unauthorize', ['as' => 'expediente.unauthorize', 'uses' => 'ExpedienteController@resign']);
Route::get('/expediente/{expediente}/show', ['as' => 'expediente.show', 'uses' => 'ExpedienteController@show']);
Route::post('/expediente/{expediente}/refuse', ['as' => 'expediente.refuse', 'uses' => 'ExpedienteController@refuse']);
Route::get('/expediente/autorizar', ['as' => 'expediente.autorizar', 'uses' => 'ExpedienteController@autorizar']);
Route::get('/expediente/pdf', ['as' => 'expediente.pdf', 'uses' => 'ExpedienteController@imprimir']);


Route::get('/actividad', ['as' => 'actividad.direccion.index', 'uses' => 'PanelActividadController@index']);
Route::get('/actividad/{actividad}/authorize', ['as' => 'actividad.authorize', 'uses' => 'ActividadController@accept']);
Route::get('/actividad/{actividad}/unauthorize', ['as' => 'actividad.unauthorize', 'uses' => 'ActividadController@resign']);
Route::get('/actividad/{actividad}/show', ['as' => 'actividad.show', 'uses' => 'ActividadController@show']);
Route::post('/actividad/{actividad}/refuse', ['as' => 'actividad.refuse', 'uses' => 'ActividadController@refuse']);
Route::get('/actividad/pdf', ['as' => 'actividad.pdf', 'uses' => 'ActividadController@printAutoritzats']);
Route::get('/actividad/autorizar', ['as' => 'actividad.autorizar', 'uses' => 'ActividadController@autorizar']);

Route::get('/falta', ['as' => 'falta.direccion.index', 'uses' => 'PanelFaltaController@index']);
Route::get('/falta/{falta}/resolve', ['as' => 'falta.resolve', 'uses' => 'FaltaController@resolve']);
Route::get('/falta/{falta}/show', ['as' => 'falta.show', 'uses' => 'FaltaController@show']);
Route::post('/falta/{falta}/refuse', ['as' => 'falta.refuse', 'uses' => 'FaltaController@refuse']);
Route::post('/falta', ['as' => 'falta.store', 'uses' => 'FaltaController@store']);
Route::put('/falta/{falta}/edit',['as' => 'falta.edit', 'uses' => 'FaltaController@update']);
Route::get('/falta/{falta}/alta', ['as' => 'falta.alta', 'uses' => 'FaltaController@alta']);
Route::get('/falta_itaca',['as' => 'faltaItaca.direccion.index', 'uses' => 'PanelFaltaItacaController@index']);
Route::get('/falta_itaca/{falta}/resolve', ['as' => 'faltaItaca.resolve', 'uses' => 'FaltaItacaController@resolve']);
Route::post('/falta_itaca/{falta}/refuse', ['as' => 'faltaItaca.refuse', 'uses' => 'FaltaItacaController@refuse']);

Route::get('/falta/pdf', ['as' => 'falta.pdf', 'uses' => 'MensualController@vistaImpresion']);
Route::post('/falta/pdf', ['as' => 'falta.pdf', 'uses' => 'MensualController@imprimir']);

Route::get('/alumno/{alumno}/edit', ['as' => 'alumno.edit', 'uses' => 'AlumnoController@edit']);
Route::get('/programacion/list', ['as' => 'programacion.list', 'uses' => 'PanelControlProgramacionController@index']);
Route::get('/fichar/control',['as' => 'fichar.control', 'uses' => 'FicharController@control']);
Route::get('/fichar/controlDia',['as' => 'ficharDia.control', 'uses' => 'FicharController@controlDia']);
Route::get('/fichar/list', ['as' => 'fichar.list', 'uses' => 'PanelPresenciaController@indice']);
Route::get('/fichar/list/{dia}', ['as' => 'fichar.list', 'uses' => 'PanelPresenciaController@indice']);
Route::get('/fichar/{usuario}/delete/{dia}',['as' => 'fichar.borrar', 'uses' => 'PanelPresenciaController@deleteDia']);
Route::get('/reunion/list', ['as' => 'reunion.list', 'uses' => 'ReunionController@listado']);
Route::post('/reunion/aviso', ['as' => 'reunion.avisaFalta','uses'=>'ReunionController@avisaFaltaActa']);
Route::get('/horarios/pdf', ['as'=>'horarios.pdf','uses'=>'ProfesorController@imprimirHorarios']);
Route::get('/infDpto', ['as'=>'infdpto.control','uses'=>'PanelInfDptoController@index']);


Route::get('/{grupo}/acta', ['as' => 'fct.acta', 'uses' => 'PanelActasController@indice']);
Route::get('/{grupo}/finActa', ['as' => 'fct.finActa', 'uses' => 'PanelActasController@finActa']);
Route::get('/{grupo}/rejectActa', ['as' => 'fct.rejectActa', 'uses' => 'PanelActasController@rejectActa']);


Route::get('/{grupo}/fol', ['as' => 'grupo.fol', 'uses' => 'GrupoController@certificados']);
Route::get('/{alumno}/aFol', ['as' => 'grupo.fol', 'uses' => 'GrupoController@certificado']);

Route::get('simplifica',['as' => 'direccion.simplifica', 'uses' => 'AdministracionController@simplifica']);

Route::get('/horarios/cambiar',['as' => 'horarios.cambiarIndex', 'uses' => 'HorarioController@changeIndex']);
Route::post('/horarios/cambiar',['as' => 'horarios.cambiar', 'uses' => 'HorarioController@changeTableAll']);

Route::get('/documento',['as'=> 'documentosP.index','uses' => 'PanelDocumentoController@index']);

Route::get('/myMail','MyMailController@create');
Route::post('/myMail','MyMailController@store');

Route::resource('/lote','LoteController', ['except' => ['destroy', 'update','show']]);
Route::post('/lote/create',['as' => 'lote.store','uses'=> 'LoteController@store']);
Route::get('/lote/{id}/print/{posicion?}',['as' => 'lote.print','uses' => 'LoteController@print']);
Route::get('/lote/{id}/capture',['as' => 'lote.capture','uses' => 'LoteController@capture']);
Route::post('/lote/{id}/capture',['as' => 'lote.capture','uses' => 'LoteController@postcapture']);

Route::view('/guardia/control', 'guardias.control');

