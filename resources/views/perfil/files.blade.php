<x-layouts.app :title="__('models.Profesor.files')">
    <x-form.profesor-files :profesor="$profesor" />

    @push('scripts')
        {{ Html::script("/js/profesor/files.js") }}
    @endpush
</x-layouts.app>
