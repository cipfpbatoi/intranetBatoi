<x-layouts.app title="Control diari fitxages">

    <div id="app">
        <control-dia-view
                :profes="{{ json_encode($profes) }}"
                :horario-inicial="{{ json_encode($horarios) }}"
        ></control-dia-view>
    </div>

    @push('scripts')
        {{ Html::script('/js/components/app.js') }}
        <!--      {{ Html::script('/js/Fichar/controlDia.js') }} -->
    @endpush
</x-layouts.app>
