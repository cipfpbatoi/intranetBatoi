@php
    $versionedScript = static function (string $path): string {
        $absolutePath = public_path(ltrim($path, '/'));
        $version = file_exists($absolutePath) ? filemtime($absolutePath) : time();

        return asset(ltrim($path, '/')) . '?v=' . $version;
    };
    $modelGridPath = '/js/' . $panel->getModel() . '/grid.js';
    $modelModalPath = '/js/' . $panel->getModel() . '/modal.js';
    $modelCreatePath = '/js/' . $panel->getModel() . '/create.js';
@endphp
@include('js.tablesjs')
<script src="{{ $versionedScript('/js/common/ui-helpers.js') }}" defer></script>
<script src="{{ $versionedScript('/js/common/api-auth.js') }}" defer></script>
<script src="{{ $versionedScript('/js/common/data-table.js') }}" defer></script>
@if (file_exists(public_path(ltrim($modelGridPath, '/'))))
    <script src="{{ $versionedScript($modelGridPath) }}" defer></script>
@else
    <script src="{{ $versionedScript('/js/grid.js') }}" defer></script>
@endif

@if (file_exists(public_path(ltrim($modelModalPath, '/'))))
    <script src="{{ $versionedScript($modelModalPath) }}" defer></script>
@else
    @if (file_exists(public_path(ltrim($modelCreatePath, '/'))))
        <script src="{{ $versionedScript($modelCreatePath) }}" defer></script>
    @endif
@endif
<script src="{{ $versionedScript('/js/delete.js') }}" defer></script>
<script src="{{ $versionedScript('/js/indexModal.js') }}" defer></script>
<script src="{{ $versionedScript('/js/datepicker.js') }}" defer></script>
