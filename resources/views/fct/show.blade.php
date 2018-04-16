@extends('layouts.intranet')
@section('css')
<title>FCT {{$fct->Alumno->FullName}} - {{$fct->Colaboracion->Centro->nombre}}</title>
@endsection
@section('content')
<div class="col-md-3 col-sm-3 col-xs-12 profile_left">
    <h3><a href='/empresa/{{$fct->Colaboracion->Centro->idEmpresa}}/detalle'>{{$fct->Colaboracion->Centro->nombre}}</a></h3>
    <h4>ID : {{$fct->id}}</h4>
    <h4>CIF : {{$fct->Colaboracion->Centro->Empresa->cif}}</h4>
    <h4>{{trans('validation.attributes.concierto')}} : {{$fct->Colaboracion->Centro->Empresa->concierto}}</h4>
    <ul class="list-unstyled user_data">
        <li><i class="fa fa-map-marker user-profile-icon"></i> {{ $fct->Colaboracion->Centro->direccion }}, {{$fct->Colaboracion->Centro->localidad}}
        </li>
        <li>
            <i class="fa fa-phone user-profile-icon"></i> {{ $fct->Colaboracion->telefono }}
        </li>
        <li class="m-top-xs">
            <i class="fa fa-envelope user-profile-icon"></i> {{ $fct->Colaboracion->email }}
        </li>
        <li class="m-top-xs">
            <i class="fa fa-calendar-o user-profile-icon"></i> {{$fct->desde}} - {{$fct->hasta}}
        </li>
        <li class="m-top-xs">
            <i class="fa fa-calendar-times-o user-profile-icon"></i> {{$fct->horas}} 
        </li>
        
    </ul>
    <a href="/fct/{{$fct->id}}/edit" class="btn btn-success"><i class="fa fa-edit m-right-xs"></i>Editar</a>
    <a href="/fct" class="btn btn-success"><i class="fa fa-arrow-left m-right-xs"></i>Volver</a>
    <br />

    <!-- start skills -->
    <h4>{{trans('validation.attributes.estado')}}</h4>
    <ul class="list-unstyled user_data">
        <li class="m-top-xs"><i class='fa fa-columns user-profile-icon'></i> {{$fct->Qualificacio}}</li>
        
    </ul>
    <!-- end of skills -->

</div>
<div class="col-md-9 col-sm-9 col-xs-12">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" @if ($activa == 1) class="active" @endif><a href="#tab_content2" id="instructor-tab" role="tab" data-toggle="tab" aria-expanded="true">{{trans('models.modelos.Instructor')}}</a>
            </li>
            <li role="presentation" @if ($activa == 3) class="active" @endif><a href="#tab_content3" id="fct-tab" role="tab" data-toggle="tab" aria-expanded="true">{{trans('models.modelos.Fct')}}</a>
            </li>
            @if ($proyecto)
                <li role="presentation" @if ($activa == 2 ) class="active" @endif><a href="#tab_content1" role="tab" id="proyecto-tab" data-toggle="tab" aria-expanded="false">{{trans('models.modelos.Proyecto')}}</a>
                </li>
            @endif
            <li role="presentation" @if ($activa == 4 ) class="active" @endif><a href="#tab_content4" role="tab" id="alumno-tab" data-toggle="tab" aria-expanded="false">{{trans('models.modelos.Alumno')}}</a>
            </li>
            
        </ul>
        <div id="myTabContent" class="tab-content">
            @if ($activa == 1)
                <div role="tabpanel" class="tab-pane fade active in" id="tab_content2" aria-labelledby="instructor-tab">
            @else    
                <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="instructor-tab">
            @endif        
                @include('fct.partials.instructores')
                </div>
            
            @if ($proyecto)
                @if ($activa == 2)
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="proyecto-tab">
                @else    
                    <div role="tabpanel" class="tab-pane fade" id="tab_content1" aria-labelledby="proyecto-tab">
                @endif 
                    @include('fct.partials.proyecto')
                    </div>
            @endif
            
            @if ($activa == 3)
                <div role="tabpanel" class="tab-pane fade active in" id="tab_content3" aria-labelledby="fct-tab">
            @else    
                <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="fct-tab">
            @endif 
                @include('fct.partials.fcts')
                </div>
            
            @if ($activa == 4)
                <div role="tabpanel" class="tab-pane fade active in" id="tab_content4" aria-labelledby="alumno-tab">
            @else    
                <div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="alumno-tab">
            @endif 
                @include('fct.partials.alumno')   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
FCT: {{$fct->Alumno->fullName}} 
@endsection

