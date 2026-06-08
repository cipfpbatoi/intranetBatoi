<?php

Route::view('/incidencia', 'incidencia.livewire-panel')->name('incidencia.mantenimiento.index');
Route::get(
    '/incidencia/{incidencia}/authorize',
    ['as' => 'incidencia.authorize', 'uses' => 'MantenimientoIncidenciaController@accept']
);
Route::get(
    '/incidencia/{incidencia}/unauthorize',
    ['as' => 'incidencia.unauthorize', 'uses' => 'MantenimientoIncidenciaController@resign']
);
Route::get('/incidencia/{incidencia}/show', ['as' => 'mantenimiento.incidencia.show', 'uses' => 'IncidenciaController@show']);
Route::post(
    '/incidencia/{incidencia}/resolve',
    ['as' => 'incidencia.resolve', 'uses' => 'MantenimientoIncidenciaController@resolve']
);
Route::post('/incidencia/{incidencia}/refuse', ['as' => 'incidencia.refuse', 'uses' => 'MantenimientoIncidenciaController@refuse']);
Route::get(
    '/incidencia/{incidencia}/orden',
    ['as' => 'incidencia.orden', 'uses' => 'MantenimientoIncidenciaController@generarOrden']
);
Route::get('/incidencia/{incidencia}/remove',
    ['as' => 'incidencia.remove', 'uses' => 'MantenimientoIncidenciaController@removeOrden']
);

Route::resource('/ordentrabajo', 'OrdenTrabajoController', ['except' => ['show', 'destroy', 'update']]);
Route::get('/ordentrabajo/{orden}/delete', ['as' => 'orden.destroy', 'uses' => 'OrdenTrabajoController@destroy']);
Route::post('/ordentrabajo/create', ['as' => 'orden.store', 'uses' => 'OrdenTrabajoController@store']);
Route::put('/ordentrabajo/{orden}/edit', ['as' => 'orden.update', 'uses' => 'OrdenTrabajoController@update']);
Route::get('/ordentrabajo/{orden}/anexo', ['as' => 'orden.anexo', 'uses' => 'PanelOrdenTrabajoController@indice']);
Route::get('/ordentrabajo/{orden}/pdf', ['as' => 'orden.pdf', 'uses' => 'OrdenTrabajoController@imprime']);
Route::get('/ordentrabajo/{orden}/resolve', ['as' => 'orden.resolve', 'uses' => 'OrdenTrabajoController@resolve']);
Route::get('/ordentrabajo/{orden}/open', ['as' => 'orden.open', 'uses' => 'OrdenTrabajoController@open']);


Route::get('materialBaja', ['as' => 'material.baja', 'uses' => 'MaterialBajaController@index']);
