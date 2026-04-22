<!-- Modal Nou -->
<x-modal name="seleccion" title='Selecciona elements' action="/grupo/list/"
         message='{{ __("messages.buttons.confirmar")}}'>
        <table id="tableSeleccion"></table>
</x-modal>
{{ Html::script("/js/Grupo/selecciona.js", ['defer' => true]) }}
{{ Html::script("/js/taulaCheck.js", ['defer' => true]) }}
