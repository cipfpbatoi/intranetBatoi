@php
    $modeloCreateScript = "/js/$modelo/edit.js";
    $hasCustomScript = file_exists(public_path($modeloCreateScript));
@endphp
 <x-layouts.app :title="__('Perfil d\'usuari')">
    {{ $formulario->render('put', 'perfil.partials.roles') }}
    @push('scripts')
        @if ($hasCustomScript)
            <script src="{{ asset($modeloCreateScript) }}"></script>
        @endif
        <script src="{{ asset('js/datepicker.js') }}"></script>
    @endpush
</x-layouts.app>
