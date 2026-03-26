<!DOCTYPE html>
<html>
<head>
    <x-layouts.meta />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite('resources/assets/sass/app.scss')
    <style>
        .iconopequeno { width: 25px; height: 25px; }
        .iconomediano { width: 30px; height: 30px; }
    </style>
    <title>@yield('titulo')</title>
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
@php
    $sectionJsMode = trim((string) $__env->yieldContent('js_mode'));
    $resolvedJsMode = in_array($sectionJsMode, ['legacy', 'hybrid', 'vite'], true)
        ? $sectionJsMode
        : 'hybrid';
    $skipAppJs = View::hasSection('skip_app_js');
@endphp

@if ($resolvedJsMode === 'vite')
    @vite('resources/assets/js/legacy-app.js')
    @unless($skipAppJs)
        @vite('resources/assets/js/app.js')
    @endunless
    @unless(View::hasSection('skip_legacy_js'))
        @vite('resources/assets/js/ppIntranet.js')
    @endunless
@else
    @vite('resources/assets/js/legacy-app.js')
    @unless(View::hasSection('skip_legacy_js'))
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
