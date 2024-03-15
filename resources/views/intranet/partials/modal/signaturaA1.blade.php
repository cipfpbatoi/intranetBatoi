<!-- Modal Nou -->
<x-modal name="signaturaA1" title='Signatura annexes' action="/externalAuth"
         message='Selecciona'>
    <input type="hidden" name="FA1" value="on">
    <input type="hidden" name="accion" value="A2" />
    <table id="tableSignaturaA1"></table>
    <br/>
    <label class="control-label" for="password">Introduir Password SAO:</label>
    <input type="password" id="password" name="password" class="form-control"/>
    @include('layouts.partials.error')
</x-modal>
