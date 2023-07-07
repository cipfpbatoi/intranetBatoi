@extends('layouts.intranet')
@section('css')
<title>{{$fct->Colaboracion->Centro->nombre}}</title>
@endsection
@section('content')
<div class="col-md-3 col-sm-3 col-xs-12 profile_left">
    <h3>
        <a href='/empresa/{{$fct->Colaboracion->Centro->idEmpresa}}/detalle'>
            {{$fct->Colaboracion->Centro->nombre}}
        </a>
    </h3>
    <h5>ID : {{$fct->id}}</h5>
    @if ($fct->asociacion == 3)
        <h5>DUAL</h5>
    @endif
    <h5><i class="fa fa-user"></i> {{$fct->Instructor->nombre}}</h5>
    <h5><i class="fa fa-credit-card"></i> {{$fct->Instructor->dni}}</h5>
    <h5><i class="fa fa-envelope"></i> {{ $fct->Instructor->email }}</h5>
    @if ($fct->Instructor->telefono != '')
        <h5><i class="fa fa-phone"></i> {{ $fct->Instructor->telefono}}</h5>
    @endif
    <hr/>
    <h5>CIF : {{$fct->Colaboracion->Centro->Empresa->cif}}</h5>
    <h5>@lang("validation.attributes.concierto") : {{$fct->Colaboracion->Centro->Empresa->concierto}}</h5>
    <ul class="list-unstyled user_data">
        <li>
            <i class="fa fa-map-marker"></i>
            {{ $fct->Colaboracion->Centro->direccion }}, {{$fct->Colaboracion->Centro->localidad}}
        </li>
        <li>
            <i class="fa fa-user"></i> {{ $fct->Colaboracion->contacto }}
        </li>
        @if ($fct->Instructor->telefono != $fct->Colaboracion->telefono)
            <li>
                <i class="fa fa-phone"></i> {{ $fct->Colaboracion->telefono }}
            </li>
        @endif
        @if ($fct->Instructor->email != $fct->Colaboracion->email)
            <li class="m-top-xs">
                <i class="fa fa-envelope"></i> {{ $fct->Colaboracion->email }}
            </li>
        @endif
    </ul>
    <hr/>
    @if ($fct->Colaboracion->Ciclo->TutoresFct->count() > 1)
        Cotutor : {{$fct->Cotutor->fullName??'No hay cotutor'}}
        <form action="{{route('fct.cotutor',$fct->id)}}" method="post">
            @csrf
            <select name="cotutor" id="cotutor">
                <option value="0">No hay cotutor</option>
                @foreach ($fct->Colaboracion->Ciclo->TutoresFct as $tutor)
                    <option value="{{$tutor->dni}}" {{($fct->cotutor==$tutor->dni)?'selected':''}}>
                        {{$tutor->fullName}}
                    </option>
                @endforeach
            </select>
            <input type="submit" value="Canvia" class="fa fa-user">
        </form>
        <hr/>
    @else
        <h5>Tutor : {{$fct->Tutor->fullName??'No hi ha tutor'}}</h5>
    @endif

    @if ($fct->Colaboracion->Centro->Empresa->conveniNou)
        <em class="fa fa-file-pdf-o"> A1</em><br />
    @endif
    <a href="{{ route('fct.pdf',$fct->id) }}" class="fa fa-file-pdf-o" target="_blank"> Cert.Inst.</a>
    @if ($fct->Colaboradores->count() > 0)
        <a href="{{ route('fct.colaborador',$fct->id) }}" class="fa fa-file-pdf-o" target="_blank"> Cert.Col.</a>
    @endif
</div>
<div class="col-md-9 col-sm-9 col-xs-12">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" @if ($activa == 1 ) class="active" @endif>
                <a href="#tab_content1" role="tab" id="alumno-tab" data-toggle="tab" aria-expanded="false">
                    @lang("validation.attributes.alumno")
                </a>
            </li>
            <li role="presentation" @if ($activa == 2 ) class="active" @endif>
                <a href="#tab_content2" role="tab" id="contactos-tab" data-toggle="tab" aria-expanded="false">
                    @lang("models.Colaboracion.contactos")
                </a>
            </li>
            <li role="presentation" @if ($activa == 3 ) class="active" @endif>
                <a href="#tab_content3" id="alumnado-tab" role="tab" data-toggle="tab" aria-expanded="true">
                    @lang("models.Colaboracion.fctAl")
                </a>
            </li>
            <li role="presentation" @if ($activa == 4)class="active" @endif>
                <a href="#tab_content4" id="centro-tab" role="tab" data-toggle="tab" aria-expanded="true">
                    @lang("models.Colaboracion.centro")
                </a>
            </li>
            <li role="presentation" @if ($activa == 5) class="active" @endif>
                <a href="#tab_content5" id="colaborador-tab" role="tab" data-toggle="tab" aria-expanded="true">
                    @lang("models.modelos.Colaborador")
                </a>
            </li>
        </ul>
        <div id="myTabContent" class="tab-content">
            <div role="tabpanel"
                 class="tab-pane fade @if ($activa == 1) active in @endif" id="tab_content1"
                 aria-labelledby="alumno-tab">
               @include('fct.partials.alumnos')
            </div>
            <div role="tabpanel"
                 class="tab-pane fade @if ($activa == 2) active in @endif" id="tab_content2"
                 aria-labelledby="contactos-tab">
                @include('fct.partials.contactos')
            </div>
            <div role="tabpanel"
                 class="tab-pane fade @if ($activa == 3) active in @endif" id="tab_content3"
                 aria-labelledby="alumnado-tab">
                @include('fct.partials.alumnado')
            </div>
            <div role="tabpanel"
                 class="tab-pane fade @if ($activa == 4) active in @endif" id="tab_content4"
                 aria-labelledby="centro-tab">
                @include('fct.partials.centro')
            </div>
            <div role="tabpanel"
                 class="tab-pane fade @if ($activa == 5) active in @endif" id="tab_content5"
                 aria-labelledby="colaborador-tab">
                @include('fct.partials.colaboradores')
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
FCT : {{$fct->Colaboracion->Centro->nombre}} - {{$fct->Colaboracion->Ciclo->ciclo }} ({{$fct->Alumnos->count()}})
@endsection
@section('scripts')
{{ Html::script("/js/datepicker.js") }}
{{ Html::script("/js/fct/show.js") }}
@endsection

