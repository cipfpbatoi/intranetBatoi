@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div  class="col-sm-12">
            <h4 class="brief">
                @if (esMismoDia($elemento->desde,$elemento->hasta))
                <i class="fa fa-calendar"></i> {{$elemento->desde}} - {{ substr($elemento->desde,11) }} 
                @else
                <i class="fa fa-calendar"></i> {{$elemento->desde}} - <i class="fa fa-calendar"></i> {{$elemento->hasta}}
                @endif
            </h4>
            <h6>@if ($elemento->fct) <strong> FCT</strong> @endif {{$elemento->Profesor->nombre}} {{$elemento->Profesor->apellido1}}</h6>
            <div class="left col-xs-12">
                <h5>{{substr($elemento->descripcion,0,140)}} @if (strlen($elemento->descripcion)>140) ... @endif </h5>
                <ul class="list-unstyled">
                    <li><i class="fa fa-automobile"></i> {{$elemento->tipoVehiculo}} - {{$elemento->kilometraje}} km.</li>
                    @isset($elemento->marca)<li><i class="fa fa-automobile"></i> {{ $elemento->marca}} {{$elemento->matricula}}</li>@endisset
                    <li><i class="fa fa-money"></i> {{ $elemento->total }}</li>
                </ul>
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    <a href='#' class='btn {{$elemento->estado<2?'btn-danger':'btn-success'}} btn-xs'>{{ $elemento->situacion }}</a>
                </p>
            </div>
            <div class="col-xs-12 col-sm-8 emphasis">
                @include ('intranet.partials.components.buttons',['tipo' => 'profile'])
            </div>
        </div>
    </div>
</div>
@endforeach

