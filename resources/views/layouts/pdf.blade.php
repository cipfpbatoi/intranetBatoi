<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        {{ Html::style('/css/bootstrap.min.css')}} 
        {{ Html::style('/css/pdf.css') }}
        @yield('css')
 </head>
    <body>
        <div id='wrapper'>
            @yield('content')
        </div>
    </body>
</html>
