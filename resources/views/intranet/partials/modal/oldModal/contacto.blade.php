<!-- Modal -->
<div id="dialogo" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">@lang("messages.buttons.contact") {{trans('models.modelos.'.$panel->getModel()) }}</h4>
      </div>
      <div class="modal-body">
		  <form id="formExplicacion" action="#" method="POST">
			 {{ csrf_field() }}
			 <label class="control-label" for="explicacion">@lang("messages.generic.resum"):</label>
			 <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
		  </form>
      </div>
      <div class="modal-footer">
		<button type="submit" form="formExplicacion" class="btn btn-primary">@lang("messages.buttons.confirmar")</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang("messages.buttons.cancel")</button>
      </div>
    </div>
  </div>
</div>

