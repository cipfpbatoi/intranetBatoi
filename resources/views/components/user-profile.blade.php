<div class="col-md-3 col-sm-3 col-xs-12 profile_left">
    <div class="profile_img">
        <div id="crop-avatar">
            <img class="img-responsive avatar-view" src="{{ asset('storage/fotos/'.$usuario->foto) }}" width="150"
                 height="150" alt="Foto Perfil" title="Foto Perfil">
        </div>
    </div>
    <h3>{{ $usuario->nombre }} {{ $usuario->apellido1 }}</h3>

    <ul class="list-unstyled user_data">
        <li>
            <em class="fa fa-map-marker user-profile-icon"></em> {{ $usuario->domicilio }}
        </li>
        <li class="m-top-xs">
            <em class="fa fa-envelope user-profile-icon"></em> {{ $usuario->email }}
        </li>
        @isset($usuario->Departamento->cliteral)
            <li>
                <em class="fa fa-briefcase user-profile-icon"></em> {{ $usuario->Departamento->cliteral }}
            </li>
        @endisset

    </ul>
    <h6>{!!  implode('<br/>', nameRolesUser($usuario->rol)) !!}</h6>
    <a href="/perfil" class="btn btn-success">
        <em class="fa fa-edit m-right-xs"></em>@lang("messages.menu.Perfil")
    </a>
    <br/>
</div>