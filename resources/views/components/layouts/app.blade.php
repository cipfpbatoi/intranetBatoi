<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.meta />

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite('resources/assets/sass/app.scss')
    <style>
        .iconopequeno { width: 25px; height: 25px; }
        .iconomediano { width: 30px; height: 30px; }
    </style>
    <title>{{ $title }}</title>
    @stack('styles')
    @yield('css')
</head>
<body
    class="nav-md"
    data-app-env="{{ app()->environment() }}"
    data-js-debug="{{ app()->isLocal() ? '1' : '0' }}"
    data-legacy-features="@yield('legacy_features')"
>
    @php
        $legacyApiToken = authUser()->api_token ?? '';
    @endphp
    @if (is_string($legacyApiToken) && $legacyApiToken !== '')
        <span id="_token" class="hidden" style="display:none;">{{ $legacyApiToken }}</span>
    @endif

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
@php
    $resolvedJsMode = in_array($jsMode ?? 'hybrid', ['legacy', 'hybrid', 'vite'], true)
        ? ($jsMode ?? 'hybrid')
        : 'hybrid';
    $skipAppJs = !empty($skipAppJs);
@endphp

@if ($resolvedJsMode === 'vite')
    @vite('resources/assets/js/legacy-app.js')
    @unless($skipAppJs)
        @vite('resources/assets/js/app.js')
    @endunless
    @unless(!empty($skipLegacyJs))
        @vite('resources/assets/js/ppIntranet.js')
    @endunless
@else
    @vite('resources/assets/js/legacy-app.js')
    @unless(!empty($skipLegacyJs))
        @if ($resolvedJsMode === 'legacy')
            <script src="{{ asset('js/ppIntranet.js') }}"></script>
        @else
            @vite('resources/assets/js/ppIntranet.js')
        @endif
    @endunless
    @if ($resolvedJsMode === 'hybrid' && !$skipAppJs)
        @vite('resources/assets/js/app.js')
    @endif
@endif
@yield('scripts')
@stack('scripts')
</body>
</html>
