<!-- Modal Nou -->
<x-modal name="dialogo" title='{{ trans("messages.buttons.refuse")}} {{trans("models.modelos.".$panel->getModel()) }}'
         message='{{ trans("messages.buttons.refuse")}}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.motivo"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>
