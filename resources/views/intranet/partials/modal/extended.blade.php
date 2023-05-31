<!-- Modal Nou -->
<x-modal name="seleccion" title='Selecciona elements' action="/{{ strtolower($panel->getModel())}}/selecciona"
         message='{{ trans("messages.buttons.confirmar")}}'>
        <strong>Selecciona Document:</strong>
        <select name="informe" id="informe">
                <option value="pg0301">@lang("models.Fct.pg0301")</option>
                <option value="pr0401">@lang("models.Fct.pr0401")</option>
                <option value="pr0402">@lang("models.Fct.pr0402")</option>
                <option value="pasqua">@lang("models.Fct.pasqua")</option>
                <option value="A1">@lang("models.Fct.an1")</option>
                <option value="A2">@lang("models.Fct.an2")</option>
                <option value="A3">@lang("models.Fct.an3")</option>
                <option value="A5">@lang("models.Fct.an5")</option>
        </select>
        <input type="checkbox" name="zip" id="zip" /> Zip
        <hr/>
        <table id="tableSeleccion"></table>

</x-modal>
{{ Html::script("/js/extended.js") }}
