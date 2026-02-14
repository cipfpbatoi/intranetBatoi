<x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" /><br/>
<div class="x_content">
    <form id="printCodeBars" method="POST" action="/inventario/barcode">
        @csrf
        <label for="posicion">Posició 1<sup>era</sup> etiqueta</label>
        <input id="posicion" type="text" name="posicion" value="1"/>
        <a href="#" id="printCodeBar" class="btn btn-small btn-info" ><i class="fa fa-barcode"></i></a>
        <input type="hidden" name="ids" id="idList" />
    </form>
    <table id='datamaterial' class="table table-striped" data-page-length="25" >
        <thead>
            <x-grid.header :panel="$panel">
                <th>@lang("messages.generic.inventary")</th>
            </x-grid.header>
        </thead>
        <tfoot>
            <x-grid.header :panel="$panel">
                <th>@lang("messages.generic.inventary")</th>
            </x-grid.header>
        </tfoot>
    </table>
</div>
<!-- Modal -->
<x-modal name="dialogo" title='  '
         message='{{ trans("messages.buttons.confirmar")}}'>
    <label class="control-label" for="explicacion">@lang("messages.generic.motivo"):</label>
    <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
</x-modal>
