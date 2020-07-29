<!-- Modal Nou -->
<x-modal name="password" title='{{ trans("messages.buttons.delete")}} {{trans("models.modelos.".$panel->getModel()) }}'
         message='{{ trans("messages.buttons.init")}}'>
    <label class="control-label" for="explicacion">Introduir Password:</label>
    <input type="password" id="pass" name="pass" class="form-control"/>
</x-modal>