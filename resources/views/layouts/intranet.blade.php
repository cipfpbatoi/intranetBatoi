<!DOCTYPE html>
<html>
<head>
    <x-layouts.meta />
    <link rel="stylesheet" href="{{ mix('css/gentelella.css') }}">
    <title>@yield('titulo')</title>
    @stack('styles')
    @yield('css')
</head>
<body class="nav-md">
@if (authUser())
    <div class="container body">
        <div class="main_container">
            <x-layouts.leftside />
            <x-layouts.topnav />
            @if (isset($panel))
                @include('layouts.partials.panel')
            @else
                @include('layouts.partials.content')
            @endif
            <x-layouts.footer />
        </div>
    </div>
@endif
<script src="{{ mix('js/gentelella.js') }}"></script>
<script src="{{ mix('js/ppIntranet.js') }}"></script>
@yield('scripts')
@stack('scripts')
</body>
</html>