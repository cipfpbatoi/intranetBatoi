<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <link rel="stylesheet" href="{{public_path('/css/pdf.css')}}" >
        @yield('css')
 </head>
    <body>
        <div id='wrapper'>
            @yield('content')
        </div>
        <footer>
            @yield('footer')
        </footer>
    </body>
</html>
