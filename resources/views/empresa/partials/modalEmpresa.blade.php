<!-- Modal -->
<div class="modal fade"
     id="AddEnterprise"
     tabindex="-1"
     role="dialog"
     aria-labelledby="AddCEnterpriseTitle"
     aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddEnterpriseTitle">@lang("models.modelos.Empresa")</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEnterprise" method="POST" class="agua" action="#">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type='text'
                           name='cif'
                           placeholder='CIF'
                           value="{{ old('cif') }}"
                           class='form-control' />
                    <input type='text'
                           name='concierto'
                           placeholder='@lang("validation.attributes.concierto") *'
                           value="{{ old('concierto') }}"
                           class='form-control' />
                    <input type='text'
                           name='email'
                           placeholder='@lang("validation.attributes.email") *'
                           value="{{ old('email') }}"
                           class='form-control' />
                    <input type='text'
                           name='telefono'
                           placeholder='@lang("validation.attributes.telefono") *'
                           value="{{ old('telefono') }}"
                           class='form-control' />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-dismiss="modal">Close</button>
                    <input id="submit"
                           class="btn-sm btn-danger"
                           type="submit"
                           value="@lang("messages.generic.anadir") @lang("models.modelos.Empresa") " />
                 </div>
            </form>
        </div>
    </div>
</div>
