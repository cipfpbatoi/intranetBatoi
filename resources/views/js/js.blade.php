@php
    $versionedScript = static function (string $path): string {
        $absolutePath = public_path(ltrim($path, '/'));
        $version = file_exists($absolutePath) ? filemtime($absolutePath) : time();

        return asset(ltrim($path, '/')) . '?v=' . $version;
    };
    $modelGridPath = '/js/' . $panel->getModel() . '/grid.js';
@endphp
@include('js.tablesjs')
<script src="{{ $versionedScript('/js/common/ui-helpers.js') }}" defer></script>
<script src="{{ $versionedScript('/js/common/data-table.js') }}" defer></script>
@if (file_exists(public_path(ltrim($modelGridPath, '/'))))
<script src="{{ $versionedScript($modelGridPath) }}" defer></script>
@else
<script src="{{ $versionedScript('/js/grid.js') }}" defer></script>
@endif
<script src="{{ $versionedScript('/js/delete.js') }}" defer></script>
@if ($panel->getModel() === 'Falta_profesor')
<script src="{{ $versionedScript('/js/list.js') }}" defer></script>
@endif
