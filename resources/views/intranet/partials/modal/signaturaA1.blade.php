<!-- Modal Nou -->
<x-modal name="signaturaA1" title='Signatura annexes' action="/externalAuth"
         message='Selecciona'>
    <input id="FA1" type="checkbox" class="elements" name="FA1" checked>A1
    <input id="A5" type="checkbox" class="elements" name="A5">A5
    <input type="hidden" name="accion" value="A2" />
    <table id="tableSignaturaA1"></table>
    <br/>
    <br/>
    @if(file_exists(storage_path('app/zip/'.authUser()->fileName.'.tmp')))
        <div style="border: 1px solid black;background-color:#ddd" >
            <h3 style="text-align: center">Signatura Digital</h3>
            <label class="control-label" for="password">Introduir Password Intranet:</label>
            <input type="password" id="decrypt" name="decrypt" class="form-control"/>
            <label class="control-label" for="password">Introduir Password Certificat:</label>
            <input type="password" id="cert" name="cert" class="form-control"/>
        </div>
    @endif
    <label class="control-label" for="password">Introduir Password SAO:</label>
    <input type="password" id="password" name="password" class="form-control"/>
    @include('layouts.partials.error')
</x-modal>
