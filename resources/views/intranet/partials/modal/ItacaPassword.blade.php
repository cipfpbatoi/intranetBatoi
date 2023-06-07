<!-- Modal Nou -->
<x-modal name="password" title='Password Itaca' action="/direccion/itaca/birret"
         message='Selecciona'>
    <label class="control-label" for="month">Introduix mes a tractar:</label>
    <input type="text" max="12" min="1"  name="month" value="{{date('m')-1}}" /><br/>
    <label class="control-label" for="password">Introduir Password Itaca:</label>
    <input type="password" id="password" name="password" class="form-control"/>

    @include('layouts.partials.error')
</x-modal>
