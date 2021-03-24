<!-- Modal Nou -->
<x-modal name="seleccion" title='Selecciona elements' action="/{{ strtolower($panel->getModel())}}/selecciona"
         message='{{ trans("messages.buttons.confirmar")}}'>
        <table id="tableSeleccion"></table>
</x-modal>
{{ Html::script("/js/selecciona.js") }}