<?php

use Illuminate\Support\Facades\Route;

Route::prefix('direccion/convalidacions')->name('convalidacions.direction.')->group(function (): void {
    Route::get('/', ['as' => 'index', 'uses' => 'DireccionConvalidacioController@index']);
    Route::delete('/{sollicitud}', ['as' => 'destroy', 'uses' => 'DireccionConvalidacioController@destroy'])
        ->whereNumber('sollicitud');
    Route::post('/regles/aplicar', ['as' => 'rules.apply', 'uses' => 'DireccionConvalidacioReglesController@apply']);
    Route::get('/documents/{convalidacio}', ['as' => 'download', 'uses' => 'DireccionConvalidacioController@download']);
    Route::get('/documents/{convalidacio}/prl', ['as' => 'download-prl', 'uses' => 'DireccionConvalidacioController@downloadPrl']);
    Route::get('/documents/{convalidacio}/adjunt/{document}', ['as' => 'download-attachment', 'uses' => 'DireccionConvalidacioController@downloadAttachment']);
    Route::post('/peticions/{convalidacio}/aportacio-documental', ['as' => 'staff-contribution', 'uses' => 'DireccionConvalidacioController@staffContribution']);
    Route::get('/peticions/{convalidacio}/aportacions/{aportacio}/document', ['as' => 'download-staff-contribution', 'uses' => 'DireccionConvalidacioController@downloadStaffContribution']);
    Route::put('/peticions/{convalidacio}', ['as' => 'resolve', 'uses' => 'DireccionConvalidacioController@resolve']);
    Route::post('/{sollicitud}/revisar', ['as' => 'review', 'uses' => 'DireccionConvalidacioController@review'])
        ->whereNumber('sollicitud');
    Route::get('/{sollicitud}', ['as' => 'show', 'uses' => 'DireccionConvalidacioController@show'])
        ->whereNumber('sollicitud');
});
