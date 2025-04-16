@php
    $modeloCreateScript = "/js/$modelo/edit.js";
    $hasCustomScript = file_exists(public_path($modeloCreateScript));
@endphp
 <x-layouts.app :title="__('Perfil d\'usuari')">
    {{ $formulario->render('put', 'perfil.partials.roles') }}
    @push('scripts')
        @if ($hasCustomScript)
            {{ Html::script(asset($modeloCreateScript)) }}
        @endif
        {{ Html::script("/js/datepicker.js") }}
    @endpush
</x-layouts.app>