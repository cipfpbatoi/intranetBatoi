<x-layouts.app title="Control setmanal fitxages">

<div id="app">
    <control-semana-view 
      :profes='@json($profes, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)'
    ></control-semana-view>
</div>

@push('scripts')
    {{ Html::script('/js/components/app.js') }}
<!--      {{ Html::script('/js/Fichar/control.js') }} -->
@endpush
</x-layouts.app>
