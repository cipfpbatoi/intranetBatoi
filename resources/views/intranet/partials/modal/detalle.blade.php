<!-- Modal Nou -->
<x-modal name="dialogo" title='{{ __("messages.buttons.refuse")}} {{__("models.modelos.".$panel->getModel()) }}'
         message='{{ __("messages.buttons.refuse")}}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.motivo"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>
