<!-- Modal Nou -->
<x-modal name="upload" title='Pujar Annexe III' action="#"
         message='Selecciona'>
    <div  style="border: 1px solid black;background-color:#ddd">
        <h3 style="text-align: center">A3</h3>
        <p>Per a pujar l'annex III signat per l'alumnat</p>
        <label class="control-label" for="file">Introduir A3:</label>
        <input type="file" id="file" name="file" class="form-control"/>
    </div>
    @include('layouts.partials.error')
</x-modal>
