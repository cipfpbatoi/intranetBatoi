<!-- Modal Nou -->
<x-modal name="password" title='Password Itaca' action="/direccion/itaca/birret"
         message='Selecciona'>
    <label class="control-label" for="password">Introduir Password Itaca:</label>
    <input type="password" id="password" name="password" class="form-control"/>
    @include('layouts.partials.error')
</x-modal>
