<?php

Route::get('/profesor', ['as' => 'profesor.index', 'uses' => 'ProfesorController@index']);
Route::post('/profesor/{profesor}/mensaje', ['as' => 'direccion.mensaje', 'uses' => 'ProfesorController@alerta']);


Route::view('/comision', 'comision.livewire-panel')->name('comision.direccion.index');
Route::redirect('/comision-livewire', '/direccion/comision')->name('comision.direccion.livewire');
Route::get('/comision/{comision}/gestor', ['as' => 'comision.direccion.gestor', 'uses' => 'Direccion\\Comision\\GestorController']);
Route::get('/comision/{comision}/authorize', ['as' => 'comision.authorize', 'uses' => 'ComisionController@accept']);
Route::get('/comision/{comision}/unauthorize', ['as' => 'comision.unauthorize', 'uses' => 'ComisionController@resign']);
Route::get('/comision/{comision}/show', ['as' => 'comision.dir.show', 'uses' => 'ComisionController@show']);
Route::post('/comision/{comision}/refuse', ['as' => 'comision.refuse', 'uses' => 'ComisionController@refuse']);
Route::get('/comision-livewire/pdf', ['as' => 'comision.direccion.pdf', 'uses' => 'Direccion\\Comision\\PrintController']);
Route::get('/comision-livewire/paid', ['as' => 'comision.direccion.paid', 'uses' => 'Direccion\\Comision\\PaymentPrintController']);

Route::get('/expediente', ['as' => 'expediente.direccion.index', 'uses' => 'PanelExpedienteController@index']);
Route::view('/expediente-livewire', 'expediente.livewire-panel')->name('expediente.direccion.livewire');
Route::get(
    '/expediente/{expediente}/authorize',
    ['as' => 'expediente.authorize', 'uses' => 'ExpedienteController@accept']
);
Route::get(
    '/expediente/{expediente}/unauthorize',
    ['as' => 'expediente.unauthorize', 'uses' => 'ExpedienteController@resign']
);
Route::get('/expediente/{expediente}/show', ['as' => 'expediente.show', 'uses' => 'ExpedienteController@show']);
Route::post('/expediente/{expediente}/refuse', ['as' => 'expediente.refuse', 'uses' => 'ExpedienteController@refuse']);
Route::get('/expediente/autorizar', ['as' => 'expediente.autorizar', 'uses' => 'ExpedienteController@autorizar']);
Route::get('/expediente/pdf', ['as' => 'expediente.pdf', 'uses' => 'ExpedienteController@imprimir']);


Route::view('/actividad', 'actividad.livewire-panel')->name('actividad.direccion.index');
Route::redirect('/actividad-livewire', '/direccion/actividad')->name('actividad.direccion.livewire');
Route::get('/actividad/{actividad}/gestor', ['as' => 'actividad.direccion.gestor', 'uses' => 'Direccion\\Actividad\\GestorController']);
Route::get('/actividad/{actividad}/pdfVal', ['as' => 'actividad.direccion.pdfVal', 'uses' => 'Direccion\\Actividad\\ValuePdfController']);
Route::get('/actividad/pdf', ['as' => 'actividad.pdf', 'uses' => 'Direccion\\Actividad\\PrintController']);
Route::get('/actividad/autorizar', ['as' => 'actividad.autorizar', 'uses' => 'Direccion\\Actividad\\AuthorizeController']);

Route::view('/falta', 'falta.livewire-panel')->name('falta.direccion.index');
Route::redirect('/falta-livewire', '/direccion/falta')->name('falta.direccion.livewire');
Route::get('/falta/{falta}/resolve', ['as' => 'falta.resolve', 'uses' => 'FaltaController@resolve']);
Route::get('/falta/{falta}/show', ['as' => 'falta.direccion.show', 'uses' => 'Direccion\\Falta\\ShowController']);
Route::get('/falta/{falta}/document', ['as' => 'falta.direccion.document', 'uses' => 'Direccion\\Falta\\DocumentController']);
Route::get('/falta/{falta}/delete', ['as' => 'falta.direccion.destroy', 'uses' => 'FaltaController@destroy']);
Route::post('/falta/{falta}/refuse', ['as' => 'falta.refuse', 'uses' => 'FaltaController@refuse']);
Route::get('/falta/{falta}/alta', ['as' => 'falta.alta', 'uses' => 'FaltaController@alta']);
Route::get('/falta_itaca', ['as' => 'faltaItaca.direccion.index', 'uses' => 'PanelFaltaItacaController@index']);
Route::get('/falta_itaca/{falta}/resolve', ['as' => 'faltaItaca.resolve', 'uses' => 'FaltaItacaController@resolve']);
Route::post('/falta_itaca/{falta}/refuse', ['as' => 'faltaItaca.refuse', 'uses' => 'FaltaItacaController@refuse']);

Route::get('/falta/pdf', ['as' => 'falta.pdf', 'uses' => 'MensualController@vistaImpresion']);
Route::post('/falta/pdf', ['as' => 'falta.pdf', 'uses' => 'MensualController@imprimir']);

Route::get('/alumno/{alumno}/edit', ['as' => 'alumno.edit', 'uses' => 'AlumnoController@edit']);
Route::get('/programacion/list', ['as' => 'programacion.list', 'uses' => 'PanelControlProgramacionController@index']);
Route::get('/fichar/resumen-rango', ['as' => 'fichar.resumen-rango', 'uses' => 'FicharController@resumenRango']);

Route::get('/fichar/control', ['as' => 'fichar.control', 'uses' => 'FicharController@control']);
Route::get('/fichar/controlDia', ['as' => 'ficharDia.control', 'uses' => 'FicharController@controlDia']);
Route::get('/fichar/list', ['as' => 'fichar.list', 'uses' => 'PanelPresenciaController@indice']);
Route::get('/fichar/list/{dia}', ['as' => 'fichar.list', 'uses' => 'PanelPresenciaController@indice']);
Route::get('/fichar/{usuario}/email/{dia}', ['as' => 'fichar.email', 'uses' => 'PanelPresenciaController@email']);
Route::get('/fichar/{usuario}/delete/{dia}', ['as' => 'fichar.borrar', 'uses' => 'PanelPresenciaController@deleteDia']);
Route::get('/reunion/list', ['as' => 'reunion.list', 'uses' => 'ReunionController@listado']);
Route::post('/reunion/aviso', ['as' => 'reunion.avisaFalta','uses'=>'ReunionController@avisaFaltaActa']);
Route::get('/horarios/pdf', ['as'=>'horarios.pdf','uses'=>'ProfesorController@imprimirHorarios']);
Route::get('/infDpto', ['as'=>'infdpto.control','uses'=>'PanelInfDptoController@index']);


Route::get('/{grupo}/acta', ['as' => 'fct.acta', 'uses' => 'PanelActasController@indice']);
Route::get('/{grupo}/finActa', ['as' => 'fct.finActa', 'uses' => 'PanelActasController@finActa']);
Route::get('/{grupo}/rejectActa', ['as' => 'fct.rejectActa', 'uses' => 'PanelActasController@rejectActa']);


Route::get('/{grupo}/fol', ['as' => 'grupo.fol', 'uses' => 'GrupoController@certificados']);
Route::get('/{alumno}/aFol', ['as' => 'grupo.fol', 'uses' => 'GrupoController@certificado']);


Route::get('/horarios/cambiar', ['as' => 'horarios.cambiarIndex', 'uses' => 'HorarioController@changeIndex']);
Route::post('/horarios/cambiar', ['as' => 'horarios.cambiar', 'uses' => 'HorarioController@changeTableAll']);
Route::get('/horario/propuestas', ['as' => 'horario.propuestas', 'uses' => 'HorarioController@propuestas']);
Route::get('/horario/propuesta/{dni}/{id}/aceptar', ['as' => 'horario.propuesta.aceptar', 'uses' => 'HorarioController@aceptarPropuesta']);
Route::get('/horario/propuesta/{dni}/{id}/rebutjar', ['as' => 'horario.propuesta.rebutjar', 'uses' => 'HorarioController@rebutjarProposta']);
Route::get('/horario/propuesta/{dni}/{id}/esborrar', ['as' => 'horario.propuesta.esborrar', 'uses' => 'HorarioController@esborrarProposta']);

Route::get('/documento', ['as'=> 'documentosP.index','uses' => 'PanelDocumentoController@index']);

Route::get('/myMail', 'MyMailController@create');
Route::post('/myMail', 'MyMailController@store');

Route::resource('/lote', 'LoteController', ['except' => ['destroy', 'update','show']]);
Route::post('/lote/create', ['as' => 'lote.store','uses'=> 'LoteController@store']);
Route::get('/lote/{id}/print/{posicion?}', ['as' => 'lote.print','uses' => 'LoteController@print']);
Route::get('/lote/{id}/capture', ['as' => 'lote.capture','uses' => 'LoteController@capture']);
Route::post('/lote/{id}/capture', ['as' => 'lote.capture','uses' => 'LoteController@postcapture']);

Route::get('/materialBaja', ['as' => 'materialBaja.direccion.index','uses' => 'MaterialModController@index']);
Route::get('/signatures', ['as' => 'signatura.direccion.index', 'uses' => 'PanelSignaturaController@index']);
Route::post('/signatures', ['as' => 'signatura.direccion.post', 'uses' => 'PanelSignaturaController@sign']);
// @deprecated Flux legacy d'autorització de birrets via ITACA.
Route::post('/itaca/birret', ['as'=>'itaca.birret', 'uses'=>'ItacaController@birret']);
Route::post('/itaca/faltes', ['as'=>'itaca.faltes', 'uses'=>'ItacaController@faltes']);

Route::view('/guardia/control', 'guardias.control');
Route::view('/calendari',  'calendari.escolar');
