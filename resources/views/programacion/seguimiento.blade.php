<x-layouts.app  :title="__('models.Programacion.seguimiento')">
    <x-form.dynamic-model-form
            :model="$elemento"
            method="PUT"
            :fillable="[]"
            :defaults="[]"
    >
        {!! Field::radios('criterios', ['1','2','3','4','5'], $elemento->criterios, ['inline']) !!}
        {!! Field::radios('metodologia', ['1','2','3','4','5'], $elemento->metodologia, ['inline']) !!}
        {!! Field::textarea('propuestas', $elemento->propuestas) !!}
    </x-form.dynamic-model-form>
</x-layouts.app>

