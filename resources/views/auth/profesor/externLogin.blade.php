<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Autenticacion Profesor Externa</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body class="login">
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <form method="post" action="{{ url('/profesor/extern/login') }}">
                        <h1>Login per {{$professor->fullName}}</h1>
                        {!! csrf_field() !!}
                        <input type="hidden" name="api_token" value="{{$professor->api_token}}">
                        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                            <input type="password" class="form-control" placeholder="Password" name="password">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary  btn-block btn-flat">Entra</button>
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
        </div>
    </body>
</html>
