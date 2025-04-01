<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.meta />

    <link rel="stylesheet" href="{{ mix('css/gentelella.css') }}">
    @stack('styles')
    @yield('css')
</head>
<body class="nav-md">
@if ($user)
    <div class="container body">
        <div class="main_container">

            {{-- Sidebar i top navigation --}}
            <x-layouts.leftside />
            <x-layouts.topnav />

            {{-- Contingut de la pàgina --}}
            @if (isset($panel))
                <x-layouts.panel :panel="$panel" />
            @else
                <x-layouts.page>
                    @yield('content')
                </x-layouts.page>
            @endif

            {{-- Peu de pàgina --}}
            <x-layouts.footer />
        </div>
    </div>
@endif

{{-- JS --}}
<script src="{{ mix('js/gentelella.js') }}"></script>
<script src="{{ mix('js/ppIntranet.js') }}"></script>
@yield('scripts')
@stack('scripts')
</body>
</html>