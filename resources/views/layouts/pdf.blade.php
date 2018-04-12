<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        {{ Html::style('/css/bootstrap.min.css')}} 
        {{ Html::style('/css/pdf.css') }}
        @yield('css')
 </head>
    <body>
        @if (!Auth::guest())
        <div id='wrapper'>
            @yield('content')
        </div>
        @endif
    </body>
</html>
