<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{ Html::style('/css/estilo.css')}}
    {{ Html::style('/css/app.css')}}
    @livewireStyles
</head>
<body class="nav-md">
@if (AuthUser())
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    @include('layouts.partials.topside')
                    <br />
                    @include('layouts.partials.sidebar')
                    @include('layouts.partials.footerbuttons')

                </div>
            </div>
            @include('layouts.partials.topnav')
            @include('layouts.partials.content')
            @include('layouts.partials.footer')
        </div>
    </div>
@endif
{{ HTML::script('/js/app.js') }}
@livewireScripts
</body>
</html>