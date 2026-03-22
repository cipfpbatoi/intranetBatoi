<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Autenticacion Alumno</title>
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
        </style>
    </head>
    <body class="login">
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <form method="post" action="{{ url('/alumno/login') }}">
                        <h1>Login Alumno</h1>
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <input type="nia" class="form-control {{ $errors->has('nia') ? 'is-invalid' : '' }}" name="nia" value="{{ old('nia') }}" placeholder="NIA">
                            <span class="fa fa-envelope form-control-feedback"></span>
                            @if ($errors->has('nia'))
                            <span class="invalid-feedback d-block">
                                <strong>{{ $errors->first('nia') }}</strong>
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
                        </div>
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
            <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->
    </body>
</html>
