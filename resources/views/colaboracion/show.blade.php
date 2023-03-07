@extends('layouts.intranet')
@section('css')
    <title>Colaboracion {{$elemento->id}}</title>
@endsection
@section('content')
    <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
        <h3>{{$elemento->Centro->nombre}}</h3>
        <h4>{{$elemento->Ciclo->ciclo}}</h4>
        <h4>@lang("validation.attributes.concierto") : {{$elemento->Centro->Empresa->concierto}}</h4>
        <ul class="list-unstyled user_data">
            <li><em class="fa fa-map-marker user-profile-icon"></em> {{ $elemento->Centro->direccion }}
                , {{$elemento->Centro->localidad}}
            </li>
            <li>
                <em class="fa fa-user user-profile-icon"></em> {{ $elemento->contacto }}
            </li>
            <li>
                <em class="fa fa-phone user-profile-icon"></em> {{ $elemento->telefono }}
            </li>
            <li class="m-top-xs">
                <em class="fa fa-envelope user-profile-icon"></em> {{ $elemento->email }}
            </li>
        </ul>
        <br/>

        <!-- start skills -->
        <h4>@lang("messages.generic.options")</h4>
        <ul class="list-unstyled user_data">
            <li>
                <p>@lang("validation.attributes.dual")</p>
                <div class="progress progress_sm">
                    <div class="progress-bar bg-green" role="progressbar"
                         @if ($elemento->Centro->Empresa->dual)
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
                         @if ($elemento->Centro->Empresa->menores)
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
                         @if ($elemento->Centro->Empresa->delitos)
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
                         @if ($elemento->Centro->Empresa->sao)
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
                         @if ($elemento->Centro->Empresa->copia_anexe1)
                             data-transitiongoal="100">
                        @else
                            data-transitiongoal="0">
                        @endif
                    </div>
                </div>
            </li>
            @if ($elemento->Centro->Empresa->actividad)
                <li>
                    <p><strong>@lang("messages.generic.actividades")</strong></p>
                    <p>{{$elemento->Centro->Empresa->actividad}}</p>
                </li>
            @endif
            @if ($elemento->Centro->Empresa->observaciones)
                <li>
                    <p><strong>@lang("validation.attributes.observaciones")</strong></p>
                    <p>{{$elemento->Centro->Empresa->observaciones}}</p>
                </li>
            @endif
        </ul>
        <!-- end of skills -->
    </div>
    <div class="col-md-9 col-sm-9 col-xs-12">
        <div class="" role="tabpanel" data-example-id="togglable-tabs">.
            <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                <li role="presentation"><a href="#tab_content1" role="tab" id="colaboracion-tab" data-toggle="tab"
                                           aria-expanded="false">@lang("models.Colaboracion.contactos")</a>
                </li>
                <li role="presentation"><a href="#tab_content2" id="centro-tab" role="tab" data-toggle="tab"
                                           aria-expanded="true">@lang("models.Colaboracion.fctAl")</a>
                </li>
                <li role="presentation" @if ($pestana == 3)class="active" @endif><a href="#tab_content3" id="fct-tab"
                                                                                    role="tab" data-toggle="tab"
                                                                                    aria-expanded="true">@lang("models.Colaboracion.fct")</a>
                </li>
                <li role="presentation" @if ($pestana == 4)class="active" @endif><a href="#tab_content4"
                                                                                    id="instructor-tab" role="tab"
                                                                                    data-toggle="tab"
                                                                                    aria-expanded="true">@lang("models.Colaboracion.centro")</a>
                </li>
            </ul>
            <div id="myTabContent" class="tab-content">

                <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="centro-tab">


                    <!-- start recent activity -->
                    @include('fct.partials.alumnado')

                    <!-- end recent activity -->

                </div>

                <div role="tabpanel" class="tab-pane fade " id="tab_content1" aria-labelledby="centro-tab">


                    <!-- start user projects -->
                    @include('colaboracion.partials.contactos')

                    <!-- end user projects -->

                </div>
                @if ($pestana == 3)
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content3" aria-labelledby="fct-tab">
                        @else
                            <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="fct-tab">
                                @endif


                                <!-- start user projects -->
                                @include('colaboracion.partials.fcts')

                                <!-- end user projects -->

                            </div>
                            @if ($pestana == 4)
                                <div role="tabpanel" class="tab-pane fade active in" id="tab_content4"
                                     aria-labelledby="instructor-tab">
                                    @else
                                        <div role="tabpanel" class="tab-pane fade" id="tab_content4"
                                             aria-labelledby="instructor-tab">
                                            @endif



                                            <!-- start user projects -->
                                            @include('fct.partials.centro')

                                            <!-- end user projects -->

                                        </div>
                                </div>
                    </div>
            </div>
            @endsection
            @section('titulo')
                @lang("messages.menu.Empresa"): {{$elemento->Centro->nombre}}
@endsection
@section('scripts')
    {{ Html::script("/js/Colaboracion/modal.js") }}
    {{ Html::script("/js/datepicker.js") }}
@endsection
