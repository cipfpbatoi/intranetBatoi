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
Route::get('/login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);
Route::get('/', ['as' => 'home', 'uses' => 'Auth\LoginController@login']);
Route::get('/profesor/login', ['as' => 'profesor.login', 'uses' => 'Auth\Profesor\LoginController@showLoginForm']);
Route::get('/alumno/login', ['as' => 'alumno.login', 'uses' => 'Auth\Alumno\LoginController@showLoginForm']);
Route::post('/profesor/login', ['as' => 'profesor.postlogin', 'uses' => 'Auth\Profesor\LoginController@plogin']);
Route::post('/alumno/login', ['as' => 'alumno.postlogin', 'uses' => 'Auth\Alumno\LoginController@plogin']);
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', ['as' => 'password.reset','uses' =>'Auth\ResetPasswordController@showResetForm']);
Route::post('password/reset','Auth\ResetPasswordController@reset');

//Social Login
Route::get('/login/{token}',['as' => 'login.token', 'uses' => 'Auth\LoginController@externLogin']);
Route::get('social/google/{token?}', ['as' => 'social.google', 'uses' => 'Auth\Social\SocialController@getSocialAuth']);
Route::get('social/callback/google', ['as' => 'social.callback.google', 'uses' => 'Auth\Social\SocialController@getSocialAuthCallback']);
Route::get('lang/{lang}', ['as' => 'lang.choose', 'uses' => 'AdministracionController@lang']);
