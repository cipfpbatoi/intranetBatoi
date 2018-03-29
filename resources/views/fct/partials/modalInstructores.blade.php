<!-- Modal -->
<div class="modal fade" id="AddInstructor" tabindex="-1" role="dialog" aria-labelledby="AddInstructorTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="AddInstructorTitle">{{trans("models.modelos.Instructor")}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </h4>    
            </div>
            <form method="POST" class="agua" action="/fct/{{$elemento->id}}/instructorCreate">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <select name='idInstructor' class="form-control">
                        @foreach ($elemento->Colaboracion->Centro->Instructores as $instructor)
                            <option value='{{ $instructor->dni }}'>{!! $instructor->nombre !!}</option>
                        @endforeach
                    </select>
                    <input type="text"  name="horas" placeholder="{{trans("validation.attributes.horas")}}" value="{{ old('horas') }}" class="form-control" />
                 </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input id="submit" class="boton" type="submit" value="{{trans("messages.generic.anadir")}} {{trans("models.modelos.Instructor")}} ">
                </div>
            </form>    
        </div>
    </div>
</div>
