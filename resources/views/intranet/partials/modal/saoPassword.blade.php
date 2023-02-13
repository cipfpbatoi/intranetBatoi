<!-- Modal Nou -->
<x-modal name="password" title='Password SAO' action="/externalAuth"
         message='Selecciona'>
    <label class="control-label" for="accion">Operació a realitzar:</label>
    <select class="form-control" name="accion" id="accion">
        <option value="sao.importa">Baixa FCT's</option>
        <option value="sao.compara">Comparador Dades</option>
        <option value="sao.sync">Sincronitza FCT's</option>
        <option value="sao.annexes">Baixa Annexes</option>
    </select>
    <br/>
    <label class="control-label" for="password">Introduir Password SAO:</label>
    <input type="password" id="password" name="password" class="form-control"/>
</x-modal>
