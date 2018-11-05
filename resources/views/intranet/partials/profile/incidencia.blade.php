@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief">
                <i class="fa fa-wrench"></i> id.{{$elemento->material}} {{ $elemento->descripcion }}.
            </h4>    
            @if (!empty($elemento->Observaciones)) <h5>Observacions: {{$elemento->Observaciones}}</h5>@endif
            <div class="left col-xs-12">
                <h5> <i class="fa fa-tag"></i> {{$elemento->Espacios->descripcion}}</h5>
                <h5><i class="fa fa-tag"></i> {{$elemento->Tipos->literal}}</h5>
                <ul class="list-unstyled">
                        <li><i class="fa fa-user"></i> {{$elemento->Creador->nombre}} {{$elemento->Creador->apellido1}}  </li>
                        <li><i class="fa fa-group"></i> @if (isset($elemento->Responsables->nombre)) {{$elemento->Responsables->nombre}} {{$elemento->Responsables->apellido1}} @else No assignat @endif </li>
                </ul>
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-6 emphasis">
                <p class="ratings">
                    {{$elemento->fecha}}<br/>
                    @if (isset($elemento->orden))
                    <a href="/mantenimiento/ordentrabajo/{{$elemento->orden}}/anexo" class="btn btn-primary btn-xs">@lang("validation.attributes.orden") {{$elemento->orden}}</a>
                    @endif
                </p>
                
            </div>
            <div class="col-xs-12 col-sm-6 emphasis">
                @include ('intranet.partials.buttons',['tipo' => 'profile'])
            </div>
        </div>
    </div>
</div>
@endforeach
