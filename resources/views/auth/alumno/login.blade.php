<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Autenticacion Alumno</title>
        {{ Html::style('/css/app.css')}}
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
                        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                            <input type="nia" class="form-control" name="nia" value="{{ old('nia') }}" placeholder="NIA">
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            @if ($errors->has('nia'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nia') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                            <input type="password" class="form-control" placeholder="Password" name="password">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif

                        </div>
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember"> Recorda'm
                            </label>
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary  btn-block btn-flat">Entra</button>
                            <a href='/password/reset' class="btn btn-success btn-block btn-flat">Canvia Password</a>
                        </div>
                        <!--<a href="{{ url('/password/reset') }}">Olvidé mi password</a><br>-->
                        <div class="clearfix"></div>
                        <div class="separator">
                            <div class="clearfix"></div>
                            <br />
                            <div>
                                <h1><i class="fa fa-paw"></i> {{config('contacto.titulo')}}</h1>
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
