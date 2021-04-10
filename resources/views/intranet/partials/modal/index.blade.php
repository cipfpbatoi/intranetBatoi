<!-- Modal create -->
<div id="create" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{trans('models.'.$panel->getModel().'.create')}}</h4>
            </div>
            {{ $formulario->modal() }}
        </div>
    </div>
</div>
@include('intranet.partials.modal.show')
