<x-layouts.app title="Control setmanal fitxages" :skipLegacyJs="true">

<div id="app">
    <control-semana-view 
      :profes='@json($profes, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)'
    ></control-semana-view>
</div>

</x-layouts.app>
