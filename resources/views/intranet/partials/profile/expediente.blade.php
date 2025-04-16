@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div  class="col-sm-12">
            <h4 class="brief">
                <em class="fa fa-calendar"></em> {{$elemento->fecha}}
                <em class="fa fa-calendar"></em> {{$elemento->fechasolucion}}
            </h4>
            <div class="left col-xs-12">
                <h5>{{substr($elemento->explicacion,0,140)}} @if (strlen($elemento->explicacion)>140) ... @endif </h5>
                <ul class="list-unstyled">
                    <li><em class="fa fa-user"></em> {{$elemento->Alumno->fullName}} </li>
                    <li><em class="fa fa-user-plus">Tutor</em> {{$elemento->Profesor->fullName}} </li>
                    @if ($elemento->idAcompanyant)
                    <li><em class="fa fa-user-plus">Acompanyant</em> {{$elemento->Acompanyant->fullName}}</li>
                    @endif
                </ul>
                <h6>{{$elemento->Modulo->literal??''}} - {{ $elemento->Alumno->Grupo->first()->codigo??''}})</h6>
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    @if ($elemento->estado<2) <a href='#' class='btn btn-danger btn-xs' >
                    @else <a href='#' class='btn btn-success btn-xs' >   
                    @endif    
                    {{ $elemento->situacion }}</a>
                    <a href='#' class='btn btn-primary btn-xs' >{{ $elemento->Tipo }}</a>
                </p>
            </div>
            <div class="col-xs-12 col-sm-8 emphasis">
                <x-botones :panel="$panel" tipo="profile" :elemento="$elemento ?? null" /><br/>
             </div>
        </div>
    </div>
</div>
@endforeach
@include('intranet.partials.modal.explicacion')
