@extends('layouts.intranet')
@section('css')
<title>Panel de control</title>
@endsection
@section('content')
<div class="col-md-3 col-sm-3 col-xs-12 profile_left">
    <div class="profile_img">
        <div id="crop-avatar">
            <!-- Current avatar -->
            <img class="img-responsive avatar-view" src="{{ asset('storage/'.AuthUser()->foto) }}" width="150" height="150" alt="Foto Perfil" title="Foto Perfil">
        </div>
    </div>
    <h3>{{$usuario->nombre}} {{$usuario->apellido1}}</h3>

    <ul class="list-unstyled user_data">
        <li><i class="fa fa-map-marker user-profile-icon"></i> {{ $usuario->domicilio }}
        </li>

        <li class="m-top-xs">
            <i class="fa fa-envelope user-profile-icon"></i> {{ $usuario->email }}
        </li>
    </ul>

    <a href="/alumno/perfil" class="btn btn-success"><i class="fa fa-edit m-right-xs"></i>@lang("messages.menu.Perfil")</a>
    <br />

    <!-- start skills -->
    
    <!-- end of skills -->

</div>
<div class="col-md-9 col-sm-9 col-xs-12">

    
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">@lang("messages.generic.nextActivities")</a>
            </li>
            <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">@lang("messages.generic.timeTable")</a>
            </li>
        </ul>
        <div id="myTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">

                <!-- start recent activity -->
                @include('home.partials.activities')
                <!-- end recent activity -->

            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                @include('home.partials.horario.grupo')
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
@lang("messages.menu.Usuario")
@endsection

