<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <title></title>
        {{ Html::style('/css/pdf.css')}}
        @yield('css')
 </head>
    <body>
        <div id='wrapper'>
            @yield('content')
        </div>
    </body>
</html>
