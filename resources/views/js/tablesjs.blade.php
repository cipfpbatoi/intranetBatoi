@php
    $versionedScript = static function (string $path): string {
        $absolutePath = public_path(ltrim($path, '/'));
        $version = file_exists($absolutePath) ? filemtime($absolutePath) : time();

        return asset(ltrim($path, '/')) . '?v=' . $version;
    };
    $modelIndexPath = '/js/' . $panel->getModel() . '/index.js';
@endphp
{{ Html::script("/js/common/api-auth.js", ['defer' => true]) }}
{{ Html::script("/js/common/ui-helpers.js", ['defer' => true]) }}
@if (file_exists(public_path(ltrim($modelIndexPath, '/'))))
    <script src="{{ $versionedScript($modelIndexPath) }}" defer></script>
@endif
{{ Html::script("/js/tabledit.js", ['defer' => true]) }}
{{ Html::script("/js/barcode.js", ['defer' => true]) }}
