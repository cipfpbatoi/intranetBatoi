<!-- Modal Nou -->
<x-modal name="password" title='Password SAO' action="/externalAuth"
         message='Selecciona'>
    <label class="control-label" for="accion">Operació a realitzar:</label>
    <select class="form-control" name="accion" id="accion">
        <option value="sao.importa">Baixa FCT's a Intranet</option>
        <option value="sao.compara">Compara Dades de Centre i Empreses</option>
        <option value="sao.sync">Sincronitza hores realitzades per l'alumnat</option>
        <option value="sao.annexes">Baixa Annexes signats per a l'Intranet</option>
        <option value="sao.a2">Baixa Annexes A2 per a signar (Pruebas No funciona)</option>
    </select>
    <br/>
    <label class="control-label" for="password">Introduir Password SAO:</label>
    <input type="password" id="password" name="password" class="form-control"/>
</x-modal>
