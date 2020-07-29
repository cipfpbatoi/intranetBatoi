<!-- Modal Nou -->
<x-modal name="dialogo" title='{{ trans("messages.buttons.contact")}} {{trans("models.modelos.".$panel->getModel()) }}'
         message='{{ trans("messages.buttons.confirmar")}}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.resum"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>
