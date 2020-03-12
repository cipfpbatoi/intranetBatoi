<!-- Modal -->
@php $ciclos = \Intranet\Entities\Ciclo::where('departamento',AuthUser()->departamento)->get() @endphp
<div class="modal fade" id="AddColaboration" tabindex="-1" role="dialog" aria-labelledby="AddColaborationTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="AddColaborationTitle">@lang("models.modelos.Colaboracion")
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </h4>    
            </div>
            <form method="POST" class="agua" action="/colaboracion/create">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <select name='idCiclo' class="form-control">
                        @foreach ($ciclos as $ciclo)
                            <option value='{{ $ciclo->id }}'>{!! $ciclo->ciclo !!}</option>
                        @endforeach
                    </select>
                    <select name='idCentro' class="form-control">
                        @foreach ($elemento->centros as $centro)
                        <option value='{{ $centro->id }}'>{!! $centro->nombre !!} ({!! $centro->direccion !!})</option>
                        @endforeach
                    </select>
                    <input type='text'  id='contacto_id' name='contacto' placeholder="@lang("validation.attributes.contacto")" value="{{ old('contacto') }}" class="form-control">
                    <input type="text"  name="telefono" placeholder="{{trans("validation.attributes.telef1")}}" value="{{ old('telefono') }}" class="form-control" />
                    <input type="text" name="email" placeholder="@lang("validation.attributes.email")" value="{{ old('email') }}" class="form-control"/>
                    <input type="text"  name="tutor" placeholer="@lang("validation.attributes.tutor")"  class="form-control" value='{{AuthUser()->nombre}} {{AuthUser()->apellido1}}'/>
                    <input type="text"  name="puestos" placeholder="@lang("validation.attributes.puestos")*" value="{{ old('puestos') }}" class="form-control" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Colaboracion") ">
                </div>
            </form>    
        </div>
    </div>
</div>
