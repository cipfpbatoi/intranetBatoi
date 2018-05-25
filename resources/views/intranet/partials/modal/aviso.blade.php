<!-- Modal -->
<div id="aviso" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">@lang("messages.buttons.avisar") {{trans('models.modelos.'.$panel->getModel()) }}</h4>
      </div>
      <div class="modal-body">
		  <form id="formAviso" action="#" method="POST">
			 {{ csrf_field() }}
			 <label class="control-label" for="explicacion">@lang("messages.generic.motivo"):</label>
			 <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
		  </form>
      </div>
        
      <div class="modal-footer">
		<button type="submit" form="formAviso" class="btn btn-primary">@lang("messages.buttons.avisar")</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang("messages.buttons.cancel")</button>
      </div>
    </div>

  </div>
</div>

