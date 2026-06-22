<!-- Modal Nou -->
<x-modal name="password" title='Password SAO' action="/externalAuth"
         message='Selecciona'>
    <label class="control-label" for="accion">Operació a realitzar:</label>
    <select class="form-control" name="accion" id="accion">
        @foreach (config('auxiliares.sao') as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
    <br/>
    <label class="control-label" for="sao-password">Introduir Password SAO:</label>
    <input type="password" id="sao-password" name="password" class="form-control"/>
    @if(file_exists(storage_path('app/zip/'.authUser()->fileName.'.tmp')))
        <div id="decrypt-fields" hidden>
            <label class="control-label" for="sao-decrypt">Introduir Password Intranet:</label>
            <input type="password" id="sao-decrypt" name="decrypt" class="form-control"/>
            <label class="control-label" for="sao-cert">Introduir Password Certificat:</label>
            <input type="password" id="sao-cert" name="cert" class="form-control"/>
        </div>
    @endif
    <x-ui.errors />

</x-modal>
