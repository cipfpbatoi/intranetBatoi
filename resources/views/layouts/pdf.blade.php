<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <link rel="stylesheet" href="{{public_path('/css/pdf.css')}}" >
        @yield('css')
    </head>
    <body>
        <header>
            @yield('header')
        </header>
        <footer>
            @yield('footer')
        </footer>
        <div id='wrapper'>
            @yield('content')
        </div>
    </body>
</html>
