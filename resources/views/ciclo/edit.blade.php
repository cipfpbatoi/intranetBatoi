<x-layouts.app title="{{trans("models.ciclo.edit")}}" >
    {{ $formulario->render('put') }}
    @push('scripts')
        {{ Html::script("/js/datepicker.js") }}
    @endpush
</x-layouts.app>


