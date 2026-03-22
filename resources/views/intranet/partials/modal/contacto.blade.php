<!-- Modal Nou -->
<x-modal name="dialogo" title='{{ __("messages.buttons.contact")}} {{__("models.modelos.".$panel->getModel()) }}'
         message='{{ __("messages.buttons.confirmar")}}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.resum"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>
