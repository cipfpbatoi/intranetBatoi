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
    @yield('css')
</head>
<body class="nav-md">
@if (authUser())
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    @include('layouts.partials.topside')
                    <br/>
                    @include('layouts.partials.sidebar')
                    @include('layouts.partials.footerbuttons')
                </div>
            </div>
            @include('layouts.partials.topnav')
            <!-- page content -->
            <div class="right_col" role="main">
                <div class="">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    @include('layouts.partials.titlecontent')
                                </div>
                                <div class="x_content">
                                    {{ $slot }}
                                </div>
                                <div class="x_content">
                                    {!! Alert::render() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /page content -->
            @include('layouts.partials.footer')
        </div>
    </div>
@endif
{{ HTML::script('/js/app.js') }}
@yield('scripts')
{{ HTML::script('/js/ppIntranet.js') }}
@livewireScripts
</body>
</html>