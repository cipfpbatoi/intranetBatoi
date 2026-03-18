<!-- Modal Nou -->
<x-modal name="password" title='{{ __("messages.buttons.delete")}} {{__("models.modelos.".$panel->getModel()) }}'
         message='{{ __("messages.buttons.init")}}'>
    <label class="control-label" for="explicacion">Introduir Password:</label>
    <input type="password" id="pass" name="pass" class="form-control"/>
</x-modal>
