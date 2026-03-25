<x-layouts.app title="Control setmanal fitxages" :skipLegacyJs="true" :skipAppJs="true" jsMode="vite">

<div id="app">
    <control-semana-view 
      profes='@json($profes, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)'
    ></control-semana-view>
</div>

@push('scripts')
    @vite('resources/assets/js/fichar-app.js')
@endpush

</x-layouts.app>
