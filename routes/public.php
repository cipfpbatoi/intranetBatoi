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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

Route::get('/docs/app-docblocks', function () {
    $path = base_path('docs/app-docblocks-index.md');

    if (!File::exists($path)) {
        abort(404, 'No s\'ha trobat el fitxer de documentacio de doc-blocks.');
    }

    $markdown = File::get($path);
    $content = Str::markdown($markdown);

    return response(
        "<!DOCTYPE html>
<html lang=\"ca\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>Doc-blocks de l'aplicacio</title>
    <style>
        :root {
            --bg: #f7f9fc;
            --surface: #ffffff;
            --text: #232f3e;
            --muted: #5f6b7a;
            --line: #dbe3ea;
            --brand: #85ea2d;
            --brand-dark: #1b1f24;
            --title: #6fbe44;
            --section-bg: #effbe6;
            --section-border: #8fd45f;
            --file-title: #0f766e;
            --class-title: #166534;
            --class-code-bg: #ecfdf3;
            --class-code-border: #b7ebc6;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font: 15px/1.6 \"Segoe UI\", \"Helvetica Neue\", Helvetica, Arial, sans-serif;
        }
        .topbar {
            background: var(--brand-dark);
            color: #fff;
            border-top: 4px solid var(--brand);
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .topbar a {
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 5px;
            padding: 6px 10px;
            font-size: 13px;
        }
        .wrapper {
            max-width: 1100px;
            margin: 24px auto;
            padding: 0 16px;
        }
        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 22px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        h1, h2, h3, h4 {
            color: #111827;
            line-height: 1.3;
            margin-top: 1.2em;
            margin-bottom: 0.6em;
        }
        h1 { margin-top: 0; border-bottom: 1px solid var(--line); padding-bottom: 10px; color: var(--title); }
        h2 {
            color: #14532d;
            background: var(--section-bg);
            border-left: 5px solid var(--section-border);
            border-radius: 6px;
            padding: 8px 10px;
        }
        h3 {
            color: var(--file-title);
            border-bottom: 1px dashed #b7d8d2;
            padding-bottom: 6px;
        }
        h4 {
            color: var(--class-title);
            margin-top: 0.9em;
        }
        p, li { color: var(--text); }
        a { color: #1976d2; }
        code {
            background: #f2f5f8;
            border: 1px solid #e3eaf1;
            border-radius: 4px;
            padding: 0.1rem 0.35rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, \"Liberation Mono\", monospace;
            font-size: 0.92em;
        }
        pre {
            background: #0f172a;
            color: #e2e8f0;
            padding: 12px;
            border-radius: 6px;
            overflow-x: auto;
            border: 1px solid #1e293b;
        }
        pre code {
            background: transparent;
            border: 0;
            color: inherit;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 14px;
        }
        th, td {
            border: 1px solid var(--line);
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f8fbff;
            color: #334155;
            font-weight: 600;
        }
        hr { border: 0; border-top: 1px solid var(--line); margin: 18px 0; }
        h3 code,
        h4 code {
            border-radius: 999px;
            padding: 0.2rem 0.55rem;
            border-width: 1px;
        }
        h3 code {
            background: #e9f8f5;
            border-color: #b8e5dc;
            color: #0f766e;
        }
        h4 code {
            background: var(--class-code-bg);
            border-color: var(--class-code-border);
            color: #166534;
        }
    </style>
</head>
<body>
    <header class=\"topbar\">
        <strong>Intranet API Docs</strong>
        <a href=\"/docs\">Obrir Swagger</a>
    </header>
    <main class=\"wrapper\">
        <section class=\"panel\">{$content}</section>
    </main>
</body>
</html>",
        200,
        ['Content-Type' => 'text/html; charset=UTF-8']
    );
})->name('docs.app-docblocks');
