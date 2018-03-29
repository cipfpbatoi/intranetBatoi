<!-- Modal -->
<div class="modal fade" id="AddFct" tabindex="-1" role="dialog" aria-labelledby="AddFctTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="AddFctTitle">{{trans("models.modelos.Fct")}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </h4>    
            </div>
            <form method="POST" class="agua" action="/colaboracion/create">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type='hidden' name='idColaboracion' value='{{ $colaboracion->id }}' >
                    <select name='idAlumno' class="form-control">
                        @foreach ($alumnos as $alumno)
                        <option value='{{ $alumno->nia }}'>{!! $alumno->FullName !!}</option>
                        @endforeach
                    </select>
                    <input type='text' id='instructor' name='instructor' placeholder="{{trans("validation.attributes.instructor")}}" value="{{ old('instructor') }}" class="form-control">
                    <input type="text"  name="dni" placeholder="{{trans("validation.attributes.dni")}}" value="{{ old('dni') }}" class="form-control" />
                    <input type="text" name="desde" placeholder="{{trans("validation.attributes.email")}}" value="{{ old('email') }}" class="form-control"/>
                    <input type="text"  name="hasta"  placeholder="{{trans("validation.attributes.dni")}}" value="{{ old('dni') }}" class="form-control" />
                    <input type="text"  name="tutor" placeholer="{{trans("validation.attributes.tutor")}}"  class="form-control" value='{{AuthUser()->nombre}} {{AuthUser()->apellido1}}'/>
                    <input type="text"  name="puestos" placeholder="{{trans("validation.attributes.puestos")}}*" value="{{ old('puestos') }}" class="form-control" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input id="submit" class="boton" type="submit" value="{{trans("messages.generic.anadir")}} {{trans("models.modelos.Colaboracion")}} ">
                </div>
            </form>    
        </div>
    </div>
</div>


