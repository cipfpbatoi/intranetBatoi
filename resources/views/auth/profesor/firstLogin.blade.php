<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Autenticacion Profesor</title>
        {{ Html::style('/css/app.css')}}
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
                        <div class="form-group has-feedback">
                            <input type="text" class="form-control" name="email" value="{{ $profesor->email }}" placeholder="Email">
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        </div>
                        <h6>Ha de tindre al meyns 8 caràcters, una majúscula, una minúscula i un número  </h6>
                        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                            <input type="password" class="form-control" placeholder="Password" name="password">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Password" name="password_confirmation">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary  btn-block btn-flat">Entra</button>
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
