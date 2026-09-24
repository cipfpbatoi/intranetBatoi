<!-- Modal Nou -->
<x-modal name="seleccion" title='Selecciona elements' action="/{{ strtolower($panel->getModel())}}/selecciona"
         message='{{ __("messages.buttons.confirmar")}}'>
        <table id="tableSeleccion"></table>
</x-modal>
{{ Html::script("/js/common/api-auth.js", ['defer' => true]) }}
<script src="{{ asset_nocache('js/selecciona.js') }}" defer></script>
{{ Html::script("/js/taulaCheck.js", ['defer' => true]) }}
