<div class='centrado'>@include('intranet.partials.buttons',['tipo' => 'index'])</div><br/>
<div class="x_content">
<table id='datamaterial' class="table table-striped" data-page-length="25">
    <thead>
    <tr>
        @foreach ($panel->getRejilla() as $item)
        <th>
            @if (strpos(trans("validation.attributes.$item"),'alidation.'))
            {{ucwords($item)}}
            @else    
            {{trans("validation.attributes.$item")}}
            @endif
        </th>
        @endforeach
        <th>{{trans("validation.attributes.operaciones")}}</th>
        <th>{{trans("messages.generic.inventary")}}</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        @foreach ($panel->getRejilla() as $item)
        <th>
            @if (strpos(trans("validation.attributes.$item"),'alidation.'))
            {{ucwords($item)}}
            @else    
            {{trans("validation.attributes.$item")}}
            @endif
        </th>
        @endforeach
        <th>{{trans("validation.attributes.operaciones")}}</th>
        <th>{{trans("messages.generic.inventary")}}</th>
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

