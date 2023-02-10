@extends('layouts.intranet')
@section('css')
    <title>Empresa {{$elemento->nombre}}</title>
@endsection
@php($centros = $elemento->centros->count())
@php($ciclo = \Intranet\Entities\Grupo::find(authUser()->GrupoTutoria)->idCiclo)
@section('content')
    <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
        <h3>{{$elemento->nombre}}</h3>
        <h4>CIF : {{$elemento->cif}}</h4>
        <h4>
            @lang("validation.attributes.concierto") : {{$elemento->concierto}}
            @if (!empty($elemento->fichero))
                <a href="/empresa/{{$elemento->id}}/document"><em class="fa fa-eye"></em></a>
            @endif
        </h4>
        @if (!empty($elemento->fichero))
                <embed
                        type="application/pdf"
                        src="/empresa/{{$elemento->id}}/document#toolbar=0&navpanes=0&scrollbar=0"
                        width="100%"
                        height="150px"
                />

        @endif
        <ul class="list-unstyled user_data">
            <li>
                <em class="fa fa-map-marker user-profile-icon"></em>
                {{ $elemento->direccion }}, {{$elemento->localidad}}
            </li>
            <li>
                <em class="fa fa-phone user-profile-icon"></em>
                {{ $elemento->telefono }}
            </li>
            <li class="m-top-xs">
                <em class="fa fa-envelope user-profile-icon"></em>
                {{ $elemento->email }}
            </li>
        </ul>
        <a href="/empresa/{{$elemento->id}}/edit" class="btn btn-success">
            <em class="fa fa-edit m-right-xs"></em>Editar</a>
        @if (esRol(authUser()->rol, config('roles.rol.jefe_practicas')))
            <a href="/empresa/{{$elemento->id}}/delete" id='Borrar' class="btn btn-danger">
                <em class="fa fa-delete m-right-xs"></em>Esborrar</a>
        @endif
        <a href="/empresa" class="btn btn-success">
            <em class="fa fa-arrow-left m-right-xs"></em>Volver</a>
        <br/>

        <!-- start skills -->

        <h4>@lang("messages.generic.options")</h4>
        <ul class="list-unstyled user_data">
            <li>
                <p>@lang("validation.attributes.dual")</p>
                <div class="progress progress_sm">
                    <div class="progress-bar bg-green" role="progressbar"
                         @if ($elemento->dual)
                             data-transitiongoal="100">
                        @else
                            data-transitiongoal="0">
                        @endif
                    </div>
                </div>
            </li>
            <li>
                <p>@lang("validation.attributes.menores")</p>
                <div class="progress progress_sm">
                    <div class="progress-bar bg-green" role="progressbar"
                         @if ($elemento->menores)
                             data-transitiongoal="100">
                        @else
                            data-transitiongoal="0">
                        @endif
                    </div>
                </div>
            </li>
            <li>
                <p>@lang("validation.attributes.delitos")</p>
                <div class="progress progress_sm">
                    <div class="progress-bar bg-green" role="progressbar"
                         @if ($elemento->delitos)
                             data-transitiongoal="100">
                        @else
                            data-transitiongoal="0">
                        @endif
                    </div>
                </div>
            </li>
            <li>
                <p>@lang("validation.attributes.sao")</p>
                <div class="progress progress_sm">
                    <div class="progress-bar bg-green" role="progressbar"
                         @if ($elemento->sao)
                             data-transitiongoal="100">
                        @else
                            data-transitiongoal="0">
                        @endif
                    </div>
                </div>
            </li>
            <li>
                <p>{{trans('validation.attributes.anexo1')}}</p>
                <div class="progress progress_sm">
                    <div class="progress-bar bg-green" role="progressbar"
                         @if ($elemento->copia_anexe1)
                             data-transitiongoal="100">
                        @else
                            data-transitiongoal="0">
                        @endif
                    </div>
                </div>
            </li>
            @if ($elemento->actividad)
                <li>
                    <p><strong>@lang("messages.generic.actividades")</strong></p>
                    <p>{{$elemento->actividad}}</p>
                </li>
            @endif
            @if ($elemento->observaciones)
                <li>
                    <p><strong>@lang("validation.attributes.observaciones")</strong></p>
                    <p>{{$elemento->observaciones}}</p>
                </li>
            @endif
        </ul>
        <!-- end of skills -->

    </div>
    <div class="col-md-9 col-sm-9 col-xs-12">
        <div class="x_panel" style="height: auto;">
            <div class="x_title">
                <h2>
                    <em class="fa fa-bars"></em>
                    Centres de treball
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link">
                            <em class="fa fa-chevron-up"></em>
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#AddCenter">
                            <em class="fa fa-plus-square-o"></em>
                        </a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                @foreach ($elemento->Centros as $centro)
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel" style="height: auto;">
                            <div class="x_title">
                                <h2>
                                    <em class="fa fa-align-left"></em>
                                    @if ($existeColaboracion = $misColaboraciones->where('idCentro',$centro->id)
                                    ->where('idCiclo',$ciclo)
                                    ->count()
                                    )
                                        <strong>{{ $centro->nombre }} / {{ $centro->localidad }} <br/></strong>
                                    @else
                                        {{ $centro->nombre }} / {{ $centro->localidad }} <br/>
                                    @endif
                                    <div class="col-md-6 col-sm-6" style="float:left">
                                        <small>
                                            <em class="fa fa-map-marker user-profile-icon"></em>
                                            {{ $centro->direccion }}
                                        </small><br/>
                                        @if ($centro->horarios)
                                            <small>
                                                <em class="fa fa-clock-o user-profile-icon"></em>
                                                {{$centro->horarios}}
                                            </small><br/>
                                        @endif
                                        @if ($centro->observaciones)
                                            <small>{{$centro->observaciones}}</small>
                                        @endif
                                    </div>


                                </h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li>
                                        <a class="collapse-link"><em class="fa fa-chevron-up"></em></a>
                                    </li>
                                    <li>
                                        <a class="centro" id="{{$centro->id}}" href="/centro/{{$centro->id}}/edit">
                                            <em class="fa fa-edit"></em>
                                        </a>
                                    </li>
                                    <li>
                                        @if (count($centro->colaboraciones)==0)
                                            <a href="/centro/{!!$centro->id!!}/delete">
                                                <em class="fa fa-trash"></em>
                                            </a>
                                        @endif
                                    </li>
                                    <li>
                                        @if  (userIsAllow(config('roles.rol.administrador')) && ($centros>1))
                                            <a onclick="editar({{$centro->id}})" >
                                                <em class="fa fa-birthday-cake"></em>
                                            </a>
                                        @endif
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" style="display: none;">
                                    @include('empresa.partials.centros')
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @include('layouts.partials.error')
    </div>
    @include('empresa.partials.modalCentro')
    @include('empresa.partials.modalColaboraciones')
    @include('empresa.partials.modalEmpresa')
@endsection
@section('titulo')
    @lang("messages.menu.Empresa"): {{$elemento->nombre}}
@endsection
@section('scripts')
    {{ Html::script('/js/Empresa/detalle.js') }}
    {{ Html::script('/js/Empresa/delete.js') }}
@endsection
