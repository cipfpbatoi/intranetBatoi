<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Pantalla Login</title>
        {{ Html::style('/css/app.css')}}  
        {{ Html::style('/css/socials.css')}} 
    </head>
    <body class="login">
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <form>
                    <h1>Panel de entrada</h1>
                    <div class="panel-body">
                        <a href="{{url('/profesor/login')}}" class="btn btn-lg waves-effect waves-light btn-block facebook">Profesor</a>
                    </div>
                    <div class="panel-body">
                        <a href="{{url('/alumno/login')}}" class="btn btn-lg waves-effect waves-light btn-block twitter">Alumno</a>
                    </div>
                    <div class="panel-body">
                        <p class="or-social">Or Use Social Login</p>
                        <a href="{{url('social/google')}}" class="btn btn-lg waves-effect waves-light btn-block google">Google+</a>
                    </div>
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


