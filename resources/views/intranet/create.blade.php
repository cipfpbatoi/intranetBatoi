@php
    $modeloCreateScript = "/js/$modelo/create.js";
    $hasCustomScript = file_exists(public_path($modeloCreateScript));
@endphp
<x-layouts.app :title="__('models.' . $modelo . '.create')">
     {{ $formulario->render('post') }}
     @push('scripts')
        @if ($hasCustomScript)
            {{ Html::script(asset($modeloCreateScript)) }}
        @endif
        {{ Html::script("/js/datepicker.js") }}
    @endpush
</x-layouts.app>