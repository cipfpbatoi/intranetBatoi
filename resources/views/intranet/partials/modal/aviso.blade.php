<!-- Modal Nou -->
    <x-modal name="aviso" title='{{ trans("messages.buttons.avisar") }} {{trans("models.modelos.".$panel->getModel())}}'
         message='{{ trans("messages.buttons.avisar") }}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.motivo"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>