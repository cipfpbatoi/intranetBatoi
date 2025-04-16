<x-layouts.app title="Control setmanal fitxages">

<div id="app">
    <control-semana-view 
      :profes="{{ json_encode($profes) }}"
    ></control-semana-view>
</div>

@push('scripts')
    {{ Html::script('/js/components/app.js') }}
<!--      {{ Html::script('/js/Fichar/control.js') }} -->
@endpush
</x-layouts.app>