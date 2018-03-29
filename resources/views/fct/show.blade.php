@extends('layouts.intranet')
@section('css')
<title>FCT {{$elemento->Alumno->FullName}} - {{$elemento->Colaboracion->Centro->nombre}}</title>
@endsection
@section('content')
<div class="col-md-3 col-sm-3 col-xs-12 profile_left">
    <h3>{{$elemento->Colaboracion->Centro->nombre}}</h3>
    <h4>ID : {{$elemento->id}}</h4>
    <h4>CIF : {{$elemento->Colaboracion->Centro->Empresa->cif}}</h4>
    <h4>{{trans('validation.attributes.concierto')}} : {{$elemento->Colaboracion->Centro->Empresa->concierto}}</h4>
    <ul class="list-unstyled user_data">
        <li><i class="fa fa-map-marker user-profile-icon"></i> {{ $elemento->Colaboracion->Centro->direccion }}, {{$elemento->Colaboracion->Centro->localidad}}
        </li>
        <li>
            <i class="fa fa-phone user-profile-icon"></i> {{ $elemento->Colaboracion->telefono }}
        </li>
        <li class="m-top-xs">
            <i class="fa fa-envelope user-profile-icon"></i> {{ $elemento->Colaboracion->email }}
        </li>
        <li class="m-top-xs">
            <i class="fa fa-calendar-o user-profile-icon"></i> {{$elemento->desde}} - {{$elemento->hasta}}
        </li>
        <li class="m-top-xs">
            <i class="fa fa-calendar-times-o user-profile-icon"></i> {{$elemento->horas}} 
        </li>
        
    </ul>
    <a href="/fct/{{$elemento->id}}/edit" class="btn btn-success"><i class="fa fa-edit m-right-xs"></i>Editar</a>
    <a href="/fct/{{$elemento->id}}/delete" id='Borrar' class="btn btn-danger"><i class="fa fa-delete m-right-xs"></i>Esborrar</a>
    <a href="/fct" class="btn btn-success"><i class="fa fa-arrow-left m-right-xs"></i>Volver</a>
    <br />

    <!-- start skills -->
    <h4>{{trans('messages.generic.estado')}}</h4>
    <ul class="list-unstyled user_data">
        
    </ul>
    <!-- end of skills -->

</div>
<div class="col-md-9 col-sm-9 col-xs-12">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" @if ($activa == 1) class="active" @endif><a href="#tab_content2" id="instructor-tab" role="tab" data-toggle="tab" aria-expanded="true">{{trans('models.modelos.Instructor')}}</a>
            </li>
            <li role="presentation" @if ($activa == 2) class="active" @endif><a href="#tab_content1" role="tab" id="proyecto-tab" data-toggle="tab" aria-expanded="false">{{trans('models.modelos.Proyecto')}}</a>
            </li>
            <li role="presentation" @if ($activa == 3) class="active" @endif><a href="#tab_content3" id="fct-tab" role="tab" data-toggle="tab" aria-expanded="true">{{trans('models.modelos.Fct')}}</a>
            </li>
            
        </ul>
        <div id="myTabContent" class="tab-content">
            @if ($activa == 1)
                <div role="tabpanel" class="tab-pane fade active in" id="tab_content2" aria-labelledby="instructor-tab">
            @else    
                <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="instructor-tab">
            @endif        
           

                <!-- start recent activity -->
                @include('fct.partials.instructores')
                
                <!-- end recent activity -->
                
            </div>
            @if ($activa == 2)
                <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="proyecto-tab">
            @else    
                <div role="tabpanel" class="tab-pane fade" id="tab_content1" aria-labelledby="instructor-tab">
            @endif 
            
                <!-- start user projects -->
                @include('fct.partials.proyecto')
               
                <!-- end user projects -->

            </div>
            @if ($activa == 3)
                <div role="tabpanel" class="tab-pane fade active in" id="tab_content3" aria-labelledby="centro-tab">
            @else    
                <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="fct-tab">
            @endif 
            
                <!-- start user projects -->
               
               
                <!-- end user projects -->

            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
FCT: {{$elemento->Alumno->fullName}} 
@endsection

