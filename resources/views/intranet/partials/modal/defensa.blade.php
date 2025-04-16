<!-- Modal per a assignar tutor, data i hora -->
<x-modal name="assignar_tutor" title='{{ trans("Assignar tutor individual, data i hora de defensa") }}'
         message='{{ trans("Omple els següents camps:") }}'>

    <!-- Seleccionar Tutor -->
    <label class="control-label" for="idProfesor">@lang("Tutor Individual"):</label>
    <select id="idProfesor" name="idProfesor" class="form-control">
        <option value="" disabled selected>@lang("Selecciona un tutor")</option>
         
    </select>

    <!-- Data de Defensa -->
    <label class="control-label mt-3" for="data">@lang("Data de Defensa"):</label>
    <input type="date" id="data" name="data" class="form-control" required>

    <!-- Hora de Defensa -->
    <label class="control-label mt-3" for="hora">@lang("Hora de Defensa"):</label>
    <input type="time" id="hora" name="hora" class="form-control" required>

</x-modal>
