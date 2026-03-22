<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Canvia el teu password</title>

        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    </head>
    <body class="login">
        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <div class="login-box">
                        <div class="login-logo">
                            <a href="{{ url('/home') }}"><b>{{ config('contacto.nombre') }}</a>
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

                                <div class="form-group">
                                    <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email"  placeholder="Email">
                                    <span class="fa fa-envelope form-control-feedback"></span>
                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback d-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary float-end">
                                            <i class="fa fa-btn fa-envelope"></i> Restablir contrasenya
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
        @vite('resources/assets/js/app.js')
    </body>
</html>
