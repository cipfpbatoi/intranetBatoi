<!-- Modal Nou -->
<x-modal name="signatura" title='Signatura annexes' action="/direccion/signatures"
         message='Selecciona'>
    <table id="tableSignatura"></table>
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
    <br/>
    @include('layouts.partials.error')
</x-modal>
{{ Html::script("/js/taulaCheck.js") }}
