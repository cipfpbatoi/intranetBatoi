<!-- Modal Nou -->
<x-modal name="seleccion" title='Selecciona elements' action="/signatura/A3/send"
         message='{{ trans("messages.buttons.confirmar")}}'>
        <table id="tableSeleccion"></table>
</x-modal>
{{ Html::script("/js/selDoc.js") }}
{{ Html::script("/js/taulaCheck.js") }}
