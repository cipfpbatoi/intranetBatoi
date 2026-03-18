<!-- Modal -->
<div class="modal fade"
     id="AddInstructor"
     tabindex="-1"
     aria-labelledby="AddInstructorTitle"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddInstructorTitle">@lang("models.modelos.Colaborador")</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" class="agua" action="/fct/{{$fct->id}}/instructorCreate">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="idFct" value="{{$fct->id}}">
                    <input type="text"
                           name='idInstructor'
                           value="{{ old('idInstructor') }}"
                           class="form-control"
                           placeholder="DNI"
                    />
                    <input type="text" name='name'  value="{{ old('name') }}" class="form-control" placeholder="Nom" />
                    <input type="text"
                           name="horas"
                           placeholder="@lang("validation.attributes.horas")"
                           value="{{ old('horas') }}"
                           class="form-control"
                    />
                 </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input id="submit"
                           class="btn btn-primary"
                           type="submit"
                           value="@lang("messages.generic.anadir") @lang("models.modelos.Colaborador") "
                    >
                </div>
            </form>
        </div>
    </div>
</div>
