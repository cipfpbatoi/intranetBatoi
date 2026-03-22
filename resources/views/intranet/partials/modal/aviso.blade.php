<!-- Modal Nou -->
    <x-modal name="aviso" title='{{ __("messages.buttons.avisar") }} {{__("models.modelos.".$panel->getModel())}}'
         message='{{ __("messages.buttons.avisar") }}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.motivo"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>
