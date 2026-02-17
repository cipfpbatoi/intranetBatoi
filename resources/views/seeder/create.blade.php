<x-layouts.app   title="Importació de dades">
    <h3>Selecciona fitxer amb les dades</h3>
    <h4>La importació es llança en segon pla. Quan faces clic en Enviar, veuràs l'estat (en cua / en procés / completada).</h4>
    <form id="formFichero" method="POST" action='/import' enctype="multipart/form-data" >
        {{ csrf_field() }}
        <label>Hi ha professors amb horari nou (no és substitut):</label> <input type='checkbox' id='primera' name='primera'/><br/>
        <label>Mode d'importació:</label>
        <select id="mode" name="mode">
            <option value="full">Complet (asíncron)</option>
            <option value="create_only">Només altes (sincrònic)</option>
        </select><br/>
        <label>Fitxer:</label><input type='file' id='fichero' name='fichero'/><br/>
        <input type='submit' class="submit" id="submit" value='Enviar'/>
    </form>
    <p><a href="{{ route('import.history') }}">Veure historial d'importacions</a></p>
    @include('intranet.partials.modal.loading')
    @push( 'scripts')
        {{ Html::script('/js/Import/create.js') }}
    @endpush
</x-layouts.app>
