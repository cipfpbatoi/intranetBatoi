@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief">
                <em class="fa fa-wrench"></em><strong> id.{{$elemento->id}}</strong>  {{$elemento->material}} {{ $elemento->descripcion }}.
            </h4>
            @if (!empty($elemento->Observaciones))
                <h5><em class="fa fa-comment-o"></em> {{$elemento->Observaciones}}</h5>
            @endif
            <div class="left col-xs-12">
                <h5> <em class="fa fa-tag"></em> {{$elemento->Xespacio}}</h5>
                <h5><em class="fa fa-tag"></em> {{$elemento->Tipos->literal}}</h5>
                <ul class="list-unstyled">
                        <li><em class="fa fa-user"></em>
                            {{$elemento->Creador->nombre}} {{$elemento->Creador->apellido1}}
                        </li>
                        <li><em class="fa fa-group"></em>
                            @if (isset($elemento->Responsables->nombre))
                                {{$elemento->Responsables->nombre}} {{$elemento->Responsables->apellido1}}
                            @else
                                No assignat
                            @endif
                        </li>
                </ul>
                @if (isset($elemento->solucion))
                    <h5><em class="fa fa-lightbulb-o"></em>{{$elemento->solucion}}</h5>
                @endif
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-6 emphasis">
                <p class="ratings">
                    {{$elemento->fecha}}<br/>
                    @if (isset($elemento->orden))
                    <a href="/mantenimiento/ordentrabajo/{{$elemento->orden}}/anexo" class="btn btn-primary btn-xs">
                        @lang("validation.attributes.orden") {{$elemento->orden}}
                    </a>
                    @endif
                </p>
                
            </div>
            <div class="col-xs-12 col-sm-6 emphasis">
                @include ('intranet.partials.components.buttons',['tipo' => 'profile'])
            </div>
        </div>
    </div>
</div>
@endforeach
