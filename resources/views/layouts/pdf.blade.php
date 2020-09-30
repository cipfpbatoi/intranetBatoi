<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <link rel="stylesheet" href="{{public_path('css/pdf.css')}}" media="all" />
        @yield('css')
 </head>
    <body>
        <div id='wrapper'>
            @yield('content')
        </div>
    </body>
</html>
