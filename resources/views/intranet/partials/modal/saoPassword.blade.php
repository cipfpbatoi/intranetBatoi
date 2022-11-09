<!-- Modal Nou -->
<x-modal name="password" title='Password SAO' action="/sao"
         message='Selecciona'>
    <label class="control-label" for="accion">Operació a realitzar:</label>
    <select class="form-control" name="accion" id="accion">
        <option value="download">Baixa FCT's</option>
        <option value="check">Comparador Dades</option>
        <option value="sync">Sincronitza FCT's</option>
        <option value="annexes">Baixa Annexes</option>
        <option value="print">Imprimeix Annexes V</option>
    </select>
    <br/>
    <label class="control-label" for="password">Introduir Password SAO:</label>
    <input type="password" id="password" name="password" class="form-control"/>
</x-modal>
