@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div  class="col-sm-12">
            <h4 class="brief">
                <i class="fa fa-calendar"></i> {{$elemento->fecha}} 
                <i class="fa fa-calendar"></i> {{$elemento->fechasolucion}} 
            <div class="left col-xs-12">
                <h5>{{substr($elemento->explicacion,0,140)}} @if (strlen($elemento->explicacion)>140) ... @endif </h5>
                <ul class="list-unstyled">
                    <li><i class="fa fa-user"></i> {{$elemento->Alumno->nombre}} {{$elemento->Alumno->apellido1}} {{$elemento->Alumno->apellido2}}</li>
                    <li><i class="fa fa-user-plus"></i> {{$elemento->Profesor->nombre}} {{$elemento->Profesor->apellido1}} {{$elemento->Profesor->apellido2}}</li>
                </ul>
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
                @include ('intranet.partials.buttons',['tipo' => 'profile'])
            </div>
        </div>
    </div>
</div>
@endforeach
@include('intranet.partials.modal.explicacion')
