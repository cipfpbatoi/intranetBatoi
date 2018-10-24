<!-- Modal -->
<div class="modal fade" id="AddInstructor" tabindex="-1" role="dialog" aria-labelledby="AddInstructorTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="AddInstructorTitle">@lang("models.modelos.Colaborador")
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </h4>    
            </div>
            <form method="POST" class="agua" action="/fct/{{$fct->id}}/instructorCreate">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <select name='idInstructor' class="form-control">
                        @foreach ($fct->Colaboracion->Centro->Instructores->whereNotIn('dni',$instructores) as $instructor)
                            <option value='{{ $instructor->dni }}'>{!! $instructor->nombre !!}</option>
                        @endforeach
                    </select>
                    <input type="text"  name="horas" placeholder="@lang("validation.attributes.horas")" value="{{ old('horas') }}" class="form-control" />
                 </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Colaborador") ">
                </div>
            </form>    
        </div>
    </div>
</div>
