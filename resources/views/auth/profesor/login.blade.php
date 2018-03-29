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
                    <form method="post" action="{{ url('/profesor/login') }}">
                        <h1>Login Profesor</h1>
                        {!! csrf_field() !!}
                        <div class="form-group has-feedback {{ $errors->has('codigo') ? ' has-error' : '' }}">
                            <input type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" placeholder="Codigo">
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            @if ($errors->has('codigo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('codigo') }}</strong>
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
                                <h1><i class="fa fa-paw"></i> CIP FP Batoi</h1>
                                <p>©2017 All Rights Reserved. Generalitat Valenciana</p>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </body>
</html>
