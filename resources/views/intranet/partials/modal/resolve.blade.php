<!-- Modal Nou -->
    <x-modal name="resolve" title='{{ __("messages.buttons.resolve") }} {{__("models.modelos.".$panel->getModel())}}'
         message='{{ __("messages.buttons.resolve") }}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.explicacion"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>
