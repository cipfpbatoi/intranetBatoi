<div class="panel">
    <a class="panel-heading" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
        <h4 class="panel-title"><i class="fa fa-bars"></i> @lang("models.Reunion.edit") {{ Intranet\Entities\TipoReunion::literal($formulario->getElemento()->tipo)}}: {{ $formulario->getElemento()->Xgrupo }}</h4>
    </a>
    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
            <div class='form_box'>
                {{ $formulario->render('PUT') }}
            </div>
        </div>
    </div>
</div>

