<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Canvia el teu password</title>

        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- Bootstrap -->
        {{ Html::style('/css/app.css')}}

    </head>
    <body class="login">
        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <div class="login-box">
                        <div class="login-logo">
                            <a href="{{ url('/home') }}"><b>{{ config('constants.contacto.nombre') }}</a>
                        </div>

                        <!-- /.login-logo -->
                        <div class="login-box-body">
                            <p class="login-box-msg">Posa el teu email per a canviar el teu password</p>

                            @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                            @endif

                            <form method="post" action="{{ url('/password/email') }}">
                                {!! csrf_field() !!}

                                <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <input type="email" class="form-control" name="email"  placeholder="Email">
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary pull-right">
                                            <i class="fa fa-btn fa-envelope"></i> Envia enllaç per canviar
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                        <!-- /.login-box-body -->
                    </div>
                </section>
            </div>
        </div>          
        {{ HTML::script('/assets/gentelella/vendors/jquery/dist/jquery.min.js') }}
        {{ HTML::script('/assets/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js') }}
    </body>
</html>
