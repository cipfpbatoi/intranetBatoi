<!-- Modal Nou -->
<x-modal name="entreFechas" title='{{ trans("messages.buttons.avisar")}} {{trans("models.modelos.".$panel->getModel()) }}'
         message='{{ trans("messages.buttons.confirmar")}}'>
    <label class="control-label" for="desde">@lang("messages.generic.desde"):</label>
    <input type='text' id="desde" name="desde" class="form-control date" value="{{hoy('d/m/Y')}}" />
    <label class="control-label" for="hasta">@lang("messages.generic.hasta"):</label>
    <input type='text' id="hasta" name="hasta" class="form-control date" value="{{hoy('d/m/Y')}}" />
</x-modal>
<script src="/js/datepicker.js"></script>