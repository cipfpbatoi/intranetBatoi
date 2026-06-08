<!-- Modal Nou -->
<x-modal name="entreFechas" title='{{ __("messages.buttons.avisar")}} {{__("models.modelos.".$panel->getModel()) }}'
         message='{{ __("messages.buttons.confirmar")}}'>
    <label class="control-label" for="desde">@lang("messages.generic.desde"):</label>
    <input type='text' id="desde" name="desde" class="form-control date" value="{{hoy('d/m/Y')}}" />
    <label class="control-label" for="hasta">@lang("messages.generic.hasta"):</label>
    <input type='text' id="hasta" name="hasta" class="form-control date" value="{{hoy('d/m/Y')}}" />
</x-modal>
<script src="/js/datepicker.js" defer></script>
