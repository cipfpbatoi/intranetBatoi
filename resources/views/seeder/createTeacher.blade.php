<x-layouts.app title="Importació de dades d'un Professor">
    <h3>Selecciona fitxer amb les dades i dni del professor a importar</h3>
    <h4>La importació es llança en segon pla i es mostrarà el seu estat en acabar l'enviament.</h4>
    <form method="POST" action='/teacherImport' enctype="multipart/form-data" >
        {{ csrf_field() }}
        <label>Mode d'importació:</label>
        <select id="mode" name="mode">
            <option value="full">Complet (asíncron)</option>
            <option value="create_only">Només altes (sincrònic)</option>
        </select><br/>
        <label>Importa horaris:</label> <input type='checkbox' id='horari' name='horari'/><br/>
        <label>Recerca en Horaris Antics:</label><input type='checkbox' id='lost' name='lost'/><br/>
        <label>Professor a importar:</label> <input type='text' id='idProfesor' name='idProfesor'/><br/>
        <label>Fitxer:</label><input type='file' id='fichero' name='fichero'/><br/>
        <input type='submit' value='Enviar'/>
    </form>
    <p><a href="{{ route('import.history') }}">Veure historial d'importacions</a></p>
</x-layouts.app>
