<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            @unless($isAlumno)
                <a href="{{ route('home') }}" class="site_title">
                    <span>{!! config('contacto.titulo') !!}</span>
                </a>
            @endunless
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div id="dni" class="hidden">{{ $user->dni }}</div>
            <div id="rol" class="hidden">{{ $user->rol }}</div>
            @isset($user->api_token)
                <div id="_token" class="hidden">{{ $user->api_token }}</div>
            @endisset

            <div class="profile_pic">
                <img src="{{ asset('/storage/fotos/' . $user->foto) }}" alt="FotoUsuari" class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <h2>{{ $user->nombre }}</h2>
            </div>

            <div class="clearfix"></div>
        </div>
        <br/>
        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                    {!! app(\Intranet\Application\Menu\MenuService::class)->make('general') !!}
                </ul>
            </div>
        </div>
        <!-- /sidebar menu -->
        <!-- /menu footer buttons -->
        <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Ajuda" target="_blank"
               href='https://cipfpbatoi.github.io/intranetBatoi/'>
                <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>
            @if (!$isAlumno)
                <a data-toggle="tooltip" data-placement="top" title="Enviar codigo fichaje" href='myApiToken'>
                    <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                </a>
                <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('logout') }}">
                    <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                </a>
            @else
                <a data-toggle="tooltip" data-placement="top" title="Lock">
                    <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                </a>
                <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('logout.alumno') }}">
                    <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                </a>
            @endif
        </div>
<!-- /menu footer buttons -->
    </div>
</div>
