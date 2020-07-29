<!-- Modal Nou -->
<x-modal name="fechas" title='Signatura' message='{{ trans("messages.buttons.confirmar")}}'>
    <label class="control-label" for="horas">Hores:</label>
    <input type='text' id="horas" name="horas" class="form-control" value="@if (isset($panel->getElementos($pestana)->first()->Colaboracion->Ciclo)) {{$panel->getElementos($pestana)->first()->Colaboracion->Ciclo->horasFct}} @endif" />
    <label class="control-label" for="desde">Data:</label>
    <input type='text' id="fecha" name="fecha" class="form-control date" value="{{hoy('d/m/Y')}}" />
</x-modal>
<script src="/js/datepicker.js"></script>
