<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.meta />

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/components/app.css') }}">
    <style>
        .iconopequeno { width: 25px; height: 25px; }
        .iconomediano { width: 30px; height: 30px; }
    </style>
    <title>{{ $title }}</title>
    @stack('styles')
    @yield('css')
</head>
<body class="nav-md">

    <div class="container body">
        <div class="main_container">

            {{-- Sidebar i top navigation --}}
            <x-layouts.leftside />
            <x-layouts.topnav />

            {{-- Contingut de la pàgina --}}
            @if (isset($panel))
                <x-layouts.panel :panel="$panel" >
                    {{ $slot }}
                </x-layouts.panel>
            @else
                <x-layouts.page :title="$title" >
                    {{ $slot }}
                </x-layouts.page>
            @endif

            {{-- Peu de pàgina --}}
            <x-layouts.footer />
        </div>
    </div>


{{-- JS --}}
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ mix('js/components/app.js') }}"></script>
@unless(!empty($skipLegacyJs))
<script src="{{ mix('js/ppIntranet.js') }}"></script>
@endunless
@yield('scripts')
@stack('scripts')
</body>
</html>
