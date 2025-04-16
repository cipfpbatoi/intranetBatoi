<!-- Modal Nou -->
<x-modal name="seleccion" title='Selecciona elements' action="/grupo/list/"
         message='{{ trans("messages.buttons.confirmar")}}'>
        <table id="tableSeleccion"></table>
</x-modal>
{{ Html::script("/js/Grupo/selecciona.js") }}
{{ Html::script("/js/taulaCheck.js") }}