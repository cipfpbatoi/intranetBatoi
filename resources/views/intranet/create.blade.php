@php
    $modeloCreateScript = "/js/$modelo/create.js";
    $hasCustomScript = file_exists(public_path($modeloCreateScript));
@endphp
<x-layouts.app :title="__('models.' . $modelo . '.create')">
    {{ $formulario->render('post') }}
     @push('scripts')
        @if ($hasCustomScript)
            <script src="{{ asset($modeloCreateScript) }}"></script>
        @endif
        <script src="{{ asset('js/datepicker.js') }}"></script>
    @endpush
</x-layouts.app>
