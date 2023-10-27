<!-- Modal Nou -->
<x-modal name="signatura" title='Signatura annexes' action="/externalAuth"
         message='Selecciona'>
    <table id="tableSignatura"></table>
    <input type="hidden" name="accion" value="A2" />
    <p>
        <strong>
            * Si no apareix una fct d'un alumne/a és perquè els documents ja han estat signats pel director/a.
            Si els vols tornar a baixar els hauràs d'esborrar.
        </strong>
    </p>
    <br/>
    @if(file_exists(storage_path('app/zip/'.authUser()->fileName.'.tmp')))
        <div style="border: 1px solid black;background-color:#ddd" >
            <h3 style="text-align: center">Signatura Digital</h3>
            <label class="control-label" for="password">Introduir Password Intranet:</label>
            <input type="password" id="decrypt" name="decrypt" class="form-control"/>
            <label class="control-label" for="password">Introduir Password Certificat:</label>
            <input type="password" id="cert" name="cert" class="form-control"/>
        </div>
    @else
        <div  style="border: 1px solid black;background-color:#ddd">
            <h3 style="text-align: center">Signatura Digital</h3>
            <p>Si vols que es signe digitalment hauràs de pujar el certificat</p>
            <label class="control-label" for="password">Introduir Password Certificat:</label>
            <input type="password" id="cert" name="cert" class="form-control"/>
            <label class="control-label" for="file">Introduir Certificat:</label>
            <input type="file" id="file" name="file" class="form-control"/>
        </div>
    @endif
    <br/>
    <label class="control-label" for="password">Introduir Password SAO:</label>
    <input type="password" id="password" name="password" class="form-control"/>
    @include('layouts.partials.error')
</x-modal>
