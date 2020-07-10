<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="{{ public_path('/css/pdf.css') }}" media="all" />
        @yield('css')
 </head>
    <body>
        <div id='wrapper'>
            @yield('content')
        </div>
    </body>
</html>
