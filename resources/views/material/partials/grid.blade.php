<div class='centrado'>@include('intranet.partials.components.buttons',['tipo' => 'index'])</div><br/>
<class="x_content">
<form id="printCodeBars" method="POST" action="/inventario/barcode">
    @csrf
    <label for="posicion">Posició 1<sup>era</sup> etiqueta</label>
    <input id="posicion" type="text" name="posicion" value="1"/>
    <a href="#" id="printCodeBar" class="btn btn-small btn-info" ><i class="fa fa-barcode"></i></a>
    <input type="hidden" name="ids" id="idList" />
</form>
<table id='datamaterial' class="table table-striped" data-page-length="25" >
    <thead>
    <tr>
        @foreach ($panel->getRejilla() as $item)
        <th scope="col">
            @if (strpos(trans("validation.attributes.$item"),'alidation.'))
            {{ucwords($item)}}
            @else    
            {{trans("validation.attributes.$item")}}
            @endif
        </th>
        @endforeach
        <th scope="col">@lang("validation.attributes.operaciones")</th>
        <th scope="col">@lang("messages.generic.inventary")</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        @foreach ($panel->getRejilla() as $item)
        <th scope="col">
            @if (strpos(trans("validation.attributes.$item"),'alidation.'))
            {{ucwords($item)}}
            @else    
            {{trans("validation.attributes.$item")}}
            @endif
        </th>
        @endforeach
        <th scope="col">@lang("validation.attributes.operaciones")</th>
        <th scope="col">@lang("messages.generic.inventary")</th>
    </tr>
    </tfoot>
</table>
</div>
<!-- Modal -->
<div id="dialogo" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
		<button type="submit" form="formExplicacion" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>

