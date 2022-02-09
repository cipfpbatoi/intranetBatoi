<!-- Modal Nou -->
<x-modal name="select" title='Trial element' action="/{{ strtolower($panel->getModel())}}/selecciona"
         message='{{ trans("messages.buttons.confirmar")}}'>
        <label class="control-label" for="seleccion">Selecciona element:</label>
        @method('put')
        <select name="idAcompanyant" id="seleccion">
                @foreach (\Intranet\Entities\Profesor::getRol(43) as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                @endforeach
        </select>
</x-modal>