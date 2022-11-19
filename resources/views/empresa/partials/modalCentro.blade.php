<!-- Modal -->
<div class="modal fade" id="AddCenter" tabindex="-1" role="dialog" aria-labelledby="AddCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddCenterTitle">@lang("models.modelos.Centro")</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" class="agua" action="/centro/create">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type='text'
                           name='nombre'
                           placeholder='@lang("validation.attributes.nombre")'
                           value="{{ old('nombre') }}"
                           class='form-control' />
                    <input type='text'
                           name='direccion'
                           placeholder='@lang("validation.attributes.direccion") *'
                           value="{{ old('direccion') }}"
                           class='form-control' />
                    <input type='text'
                           name='localidad'
                           placeholder='@lang("validation.attributes.localidad") *'
                           value="{{ old('localidad') }}"
                           class='form-control' />
                    <input type='hidden' name='idEmpresa' value="{!!$elemento->id!!}">
                    <input type='text'
                           name='horarios'
                           placeholder='@lang("validation.attributes.horarios")'
                           value="{{ old('horarios') }}"
                           class='form-control' />
                    <input type='textarea'
                           name='observaciones'
                           placeholder='@lang("validation.attributes.observaciones")'
                           value="{{ old('observaciones') }}"
                           class='form-control' />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input id="submit"
                           class="btn-primary"
                           type="submit"
                           value="@lang("messages.generic.anadir") @lang("models.modelos.Centro") " />
                 </div>
            </form>
        </div>
    </div>
</div>
