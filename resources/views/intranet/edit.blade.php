@php
    $modeloCreateScript = "/js/$modelo/edit.js";
    $hasCustomScript = file_exists(public_path($modeloCreateScript));
@endphp
<x-layouts.app :title="__('models.' . $modelo . '.edit')">
    {{ $formulario->render('put') }}
    @push('scripts')
        @if ($hasCustomScript)
            {{ Html::script(asset($modeloCreateScript)) }}
        @endif
        {{ Html::script("/js/datepicker.js") }}
    @endpush
</x-layouts.app>

