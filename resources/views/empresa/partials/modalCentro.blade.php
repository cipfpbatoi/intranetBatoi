<!-- Modal -->
<div class="modal fade" id="AddCenter" tabindex="-1" role="dialog" aria-labelledby="AddCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddCenterTitle">{{trans("models.modelos.Centro")}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" class="agua" action="/centro/create">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type='text' name='nombre' placeholder='{{trans('validation.attributes.nombre')}}' value="{{ old('nombre') }}" class='form-control' />
                    <input type='text' name='direccion' placeholder='{{trans('validation.attributes.direccion')}} *' value="{{ old('direccion') }}" class='form-control' />
                    <input type='text' name='localidad' placeholder='{{trans('validation.attributes.localidad')}} *' value="{{ old('localidad') }}" class='form-control' />
                    <input type='hidden' name='idEmpresa' value="{!!$elemento->id!!}">
                    <input type='text' name='horarios' placeholder='{{trans('validation.attributes.horarios')}}' value="{{ old('horarios') }}" class='form-control' />
<!--                <input type='text' name='telefono' placeholder='{{trans('validation.attributes.telefono')}}' value="{{ old('telefono') }}" class='form-control' />
                    <input type='text' name='email' placeholder='{{trans('validation.attributes.email')}}' value="{{ old('email') }}" class='form-control' />-->
                    <input type='textarea' name='observaciones' placeholder='{{trans('validation.attributes.observaciones')}}' value="{{ old('observaciones') }}" class='form-control' />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input id="submit" class="btn-primary" type="submit" value="{{trans("messages.generic.anadir")}} {{trans("models.modelos.Centro")}} " />
                 </div>
            </form>    
        </div>
    </div>
</div>


