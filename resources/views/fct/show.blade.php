@extends('layouts.intranet')
@section('css')
<title>{{$fct->Colaboracion->Centro->nombre}}</title>
@endsection
@section('content')
<div class="col-md-3 col-sm-3 col-xs-12 profile_left">
    <h3><a href='/empresa/{{$fct->Colaboracion->Centro->idEmpresa}}/detalle'>{{$fct->Colaboracion->Centro->nombre}}</a></h3>
    <h5>ID : {{$fct->id}} Periode : {{$fct->periode}}</h5>
    <h5><i class="fa fa-calendar-o user-profile-icon"></i> {{$fct->desde}} ({{$fct->horas}} h.)</h5>
    @if ($fct->asociacion == 3)
        <h5>DUAL</h5>
    @endif
    <h5>Instructor: {{$fct->Instructor->nombre}}</h5>
    <h5><i class="fa fa-envelope user-profile-icon"></i> {{ $fct->Instructor->email }}</h5>
    @if ($fct->Instructor->telefono != '')
        <h5><i class="fa fa-phone user-profile-icon"></i> {{ $fct->Instructor->telefono}}</h5>
    @endif    
    <hr/>
    <h5>CIF : {{$fct->Colaboracion->Centro->Empresa->cif}}</h5>
    <h5>@lang("validation.attributes.concierto") : {{$fct->Colaboracion->Centro->Empresa->concierto}}</h5>
    <ul class="list-unstyled user_data">
        <li><i class="fa fa-map-marker user-profile-icon"></i> {{ $fct->Colaboracion->Centro->direccion }}, {{$fct->Colaboracion->Centro->localidad}}
        </li>
        @if ($fct->Instructor->telefono != $fct->Colaboracion->telefono)
        <li>
            <i class="fa fa-phone user-profile-icon"></i> {{ $fct->Colaboracion->telefono }}
        </li>
        @endif
        @if ($fct->Instructor->email != $fct->Colaboracion->email)
        <li class="m-top-xs">
            <i class="fa fa-envelope user-profile-icon"></i> {{ $fct->Colaboracion->email }}
        </li>
        @endif
    </ul>
    @if ($fct->asociacion == 3)
        <a href="/dual/{{$fct->id}}/edit" class="btn btn-success"><i class="fa fa-edit m-right-xs"></i>Editar</a>
        <a href="/dual" class="btn btn-success"><i class="fa fa-arrow-left m-right-xs"></i>Volver</a>
    @else
        <a href="/fct/{{$fct->id}}/edit" class="btn btn-success"><i class="fa fa-edit m-right-xs"></i>Editar</a>
        <a href="/fct" class="btn btn-success"><i class="fa fa-arrow-left m-right-xs"></i>Volver</a>
    @endif
    <br />
</div>
<div class="col-md-9 col-sm-9 col-xs-12">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" @if ($activa == 1 ) class="active" @endif><a href="#tab_content1" role="tab" id="alumno-tab" data-toggle="tab" aria-expanded="false">@lang("models.modelos.Alumno")</a></li>
            @if ($fct->Colaboracion->Centro->Instructores->count() > 1)
            <li role="presentation" @if ($activa == 2) class="active" @endif><a href="#tab_content2" id="colaborador-tab" role="tab" data-toggle="tab" aria-expanded="true">@lang("models.modelos.Colaborador")</a></li>
            @endif
        </ul>
        <div id="myTabContent" class="tab-content">
            @if ($activa == 1)
                <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="alumno-tab">
            @else    
                <div role="tabpanel" class="tab-pane fade" id="tab_content1" aria-labelledby="alumno-tab">
            @endif        
                @include('fct.partials.alumnos')
                </div>
            @if ($fct->Colaboracion->Centro->Instructores->count() > 1)
                @if ($activa == 2)
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content2" aria-labelledby="colaborador-tab">
                @else    
                    <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="colaborador-tab">
                @endif 
                    @include('fct.partials.colaboradores')
                    </div>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
FCT : {{$fct->Colaboracion->Centro->nombre}} - {{$fct->Colaboracion->Ciclo->ciclo }} ({{$fct->Alumnos->count()}})
@endsection

