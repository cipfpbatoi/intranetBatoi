<?php

use Illuminate\Support\Facades\Route;

Route::prefix('direccion/convalidacions')->name('convalidacions.direction.')->group(function (): void {
    Route::get('/moduls', ['as' => 'modules.index', 'uses' => 'DireccionConvalidacioController@moduleRequests']);
    Route::get('/moduls/pdf', ['as' => 'modules.pdf', 'uses' => 'DireccionConvalidacioController@moduleRequestsPdf']);
    Route::post('/moduls/pdf', ['as' => 'modules.pdf.post', 'uses' => 'DireccionConvalidacioController@moduleRequestsPdf']);
});
