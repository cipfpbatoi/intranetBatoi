<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Autenticacion Profesor</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <style>
            .login-actions {
                width: 100%;
                max-width: 320px;
                margin: 16px auto 0;
            }

            .login-actions .btn {
                display: block;
                width: 100%;
                min-height: 46px;
                padding: 10px 16px;
                font-size: 16px;
                font-weight: 600;
            }

            .login-actions .btn + .btn {
                margin-top: 12px;
            }
        </style>
    </head>
    <body class="login">
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>
        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <form method="post" action="{{ url('/profesor/login') }}">
                        <h1>Login Profesor</h1>
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <input type="text" class="form-control {{ $errors->has('codigo') ? 'is-invalid' : '' }}" name="codigo" value="{{ old('codigo') }}" placeholder="Codi o email">
                            <span class="fa fa-envelope form-control-feedback"></span>
                            @if ($errors->has('codigo'))
                            <span class="invalid-feedback d-block">
                                <strong>{{ $errors->first('codigo') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Password" name="password">
                            <span class="fa fa-lock form-control-feedback"></span>
                            @if ($errors->has('password'))
                            <span class="invalid-feedback d-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember"> Recorda'm
                            </label>
                        </div>
                        <div class="login-actions">
                            <button type="submit" class="btn btn-primary w-100">Entra</button>
                            <a href='/password/reset' class="btn btn-success w-100">Canvia Password</a>
                        </div>
                        <!--<a href="{{ url('/password/reset') }}">Olvidé mi password</a><br>-->
                        <div class="clearfix"></div>
                        <div class="separator">
                            <div class="clearfix"></div>
                            <br />
                            <div>
                                <h1>{{config('contacto.titulo')}}</h1>
                                <p>©2017 All Rights Reserved. CIPFP BATOI</p>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </body>
</html>
