<!-- Modal Nou -->
<x-modal name="seleccion" title='Selecciona elements' action="/documentacionFCT/A1"
         message='{{ trans("messages.buttons.confirmar")}}'>
        <strong>Selecciona Document:</strong>
        <select name="informe" id="informe">
                <option value="">--</option>
                <option value="A1">@lang("models.Fct.an1")</option>
                <option value="A2">@lang("models.Fct.an2")</option>
        </select>
        <input type="checkbox" name="zip" id="zip" /> Zip
        <hr/>
        <table id="tableSeleccion"></table>
</x-modal>
{{ Html::script("/js/extended.js") }}
{{ Html::script("/js/taulaCheck.js") }}
