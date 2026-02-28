<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | This file is where you may define all of the routes that are handled
  | by your application. Just tell Laravel the URIs it should respond
  | to using a Closure or controller method. Build something great!
  |
 */
//Login
use Illuminate\Http\Request;
use Intranet\Entities\Profesor;

Route::get('/login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);
Route::get('/', ['as' => 'home', 'uses' => 'Auth\LoginController@login']);
Route::get('/profesor/login', ['as' => 'profesor.login', 'uses' => 'Auth\Profesor\LoginController@showLoginForm']);
Route::get('/alumno/login', ['as' => 'alumno.login', 'uses' => 'Auth\Alumno\LoginController@showLoginForm']);
Route::post('/profesor/login', ['as' => 'profesor.postlogin', 'uses' => 'Auth\Profesor\LoginController@plogin']);
Route::post('/alumno/login', ['as' => 'alumno.postlogin', 'uses' => 'Auth\Alumno\LoginController@plogin']);
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', function (Request $request) {
    $profesor =  Profesor::where('email', $request->input('email'))->first();

    if ($profesor) {
        $profesor->changePassword =  null;
        $profesor->save() ;
    }

    return redirect('/profesor/login');
});
Route::get('password/reset/{token}', ['as' => 'password.reset','uses' =>'Auth\ResetPasswordController@showResetForm']);
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::post(
    '/profesor/firstLogin',
    ['as' => 'profesor.firstLogin', 'uses' => 'Auth\Profesor\LoginController@firstLogin']
);

//Social Login
Route::get('/login/{token}', ['as' => 'login.token', 'uses' => 'Auth\ExternLoginController@showExternLoginForm']);
Route::post('/profesor/extern/login', ['as' => 'login.extern', 'uses' => 'Auth\ExternLoginController@login']);
Route::get('social/google/{token?}', ['as' => 'social.google', 'uses' => 'Auth\Social\SocialController@getSocialAuth']);
Route::get(
    'social/callback/google',
    ['as' => 'social.callback.google', 'uses' => 'Auth\Social\SocialController@getSocialAuthCallback']
);
Route::get('lang/{lang}', ['as' => 'lang.choose', 'uses' => 'AdministracionController@lang']);
Route::get('/inventario/{material}/edit', ['as' => 'inventario.edit', 'uses' => 'InventarioController@edit']);

if (!app()->environment('production')) {
    Route::get('/docs/app-docblocks', ['as' => 'docs.app-docblocks', 'uses' => 'Docs\\DocblockDocsController@index']);
}
