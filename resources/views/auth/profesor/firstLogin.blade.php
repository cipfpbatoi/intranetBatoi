<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Autenticacion Profesor</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body class="login">
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <form method="post" action="{{ url('/profesor/firstLogin') }}">
                        <h1>Primera Autenticació</h1>
                        <h6>Revisa el teu correu corporatiu</h6>
                        {!! csrf_field() !!}
                        <input type="hidden" name="codigo" value="{{ $profesor->codigo }}" />
                        <div class="form-group">
                            <input type="text" class="form-control" name="email" value="{{ $profesor->email }}" placeholder="Email">
                            <span class="fa fa-envelope form-control-feedback"></span>
                        </div>
                        <h6>Ha de tindre al meyns 8 caràcters, una majúscula, una minúscula i un número  </h6>
                        <div class="form-group">
                            <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Password" name="password">
                            <span class="fa fa-lock form-control-feedback"></span>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback d-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Password" name="password_confirmation">
                            <span class="fa fa-lock form-control-feedback"></span>
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary w-100">Entra</button>
                        </div>
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
