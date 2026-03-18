<!-- Modal create -->
 <div id="create" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title">{{trans('models.'.$panel->getModel().'.create')}}</h5>
            </div>
            {{ $formulario->modal() }}
        </div>
    </div>
</div>
