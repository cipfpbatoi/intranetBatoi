<!-- Modal Nou -->
<x-modal name="dialogo_alumno" title='Contacte Fct'
         message='{{ trans("messages.buttons.confirmar")}}'>
    <label class="control-label" for="alumnoFct">Selecciona Alumne/a:</label>
    <select id="alumnoFct" name="alumnoFct" class="form-control">
    </select>
    <label class="control-label" for="explicacion">@lang("messages.generic.resum"):</label>
    <textarea name="explicacion" class="form-control"></textarea>
</x-modal>
