@extends('layouts.intranet')
@section('css')
    <title>Empresa {{$elemento->nombre}}</title>
@endsection
@section('content')
    <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
        <h3>{{$elemento->nombre}}</h3>
        <h4>CIF : {{$elemento->cif}}</h4>
        <h4>@lang("validation.attributes.concierto") : {{$elemento->concierto}}</h4>
        @isset ($elemento->fichero)
            <embed
                    type="application/pdf"
                    src="/empresa/{{$elemento->id}}/document#toolbar=0&navpanes=0&scrollbar=0"
                    width="100%"
                    height="150px"
            />
        @endisset
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
        @if (esRol(authUser()->rol,config('roles.rol.direccion')))
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
        <div class="" role="tabpanel" data-example-id="togglable-tabs">
            <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                <li role="presentation" @if ($activa == 1) class="active" @endif>
                    <a href="#tab_content1" role="tab"
                        id="colaboracion-tab"
                        data-toggle="tab"
                        aria-expanded="false">
                        @lang("models.modelos.Colaboracion")
                    </a>
                </li>
                <li role="presentation" @if ($activa == 2) class="active" @endif>
                    <a href="#tab_content2" id="centro-tab"
                        role="tab" data-toggle="tab"
                        aria-expanded="true">
                        @lang("models.modelos.Centro")
                    </a>
                </li>
            </ul>
            <div id="myTabContent" class="tab-content">
                @if ($activa == 2)
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content2" aria-labelledby="centro-tab">
                @else
                    <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="centro-tab">
                @endif
                    <!-- start recent activity -->
                    @include('empresa.partials.centros')
                    <!-- end recent activity -->
                    </div>
                @if ($activa == 1)
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1"
                         aria-labelledby="centro-tab">
                @else
                    <div role="tabpanel" class="tab-pane fade" id="tab_content1"
                         aria-labelledby="colaboracion-tab">
                @endif

                    <!-- start user projects -->
                    @include('empresa.partials.colaboraciones')

                    <!-- end user projects -->

                    </div>

                </div>
            </div>
        </div>
@endsection
@section('titulo')
    @lang("messages.menu.Empresa"): {{$elemento->nombre}}
@endsection
@section('scripts')
    {{ Html::script('/js/Empresa/detalle.js') }}
    {{ Html::script('/js/Empresa/delete.js') }}
@endsection
