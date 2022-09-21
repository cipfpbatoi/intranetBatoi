<!-- Modal Nou -->
    <x-modal name="resolve" title='{{ trans("messages.buttons.resolve") }} {{trans("models.modelos.".$panel->getModel())}}'
         message='{{ trans("messages.buttons.resolve") }}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.explicacion"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>