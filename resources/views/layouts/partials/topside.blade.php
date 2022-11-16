@if (authUser()->rol % config('roles.rol.alumno') == 0)
    <div class="navbar nav_title" style="border: 0;"></div>
@else
    <div class="navbar nav_title" style="border: 0;">
        <a href="/home" class="site_title"><span>{!! config('contacto.titulo') !!}</span></a>
    </div>
@endif


<div class="clearfix"></div>

<!-- menu profile quick info -->
<div class="profile clearfix">
    <div id="dni" class="hidden">{!! authUser()->dni !!}</div>
    <div id="rol" class="hidden">{!! authUser()->rol !!}</div>
    @if (isset(authUser()->api_token))
        <div id="_token" class="hidden">{!! authUser()->api_token !!}</div>
    @endif
    <div class="profile_pic">
        <img src="{{ asset('/storage/'.authUser()->foto) }}" alt="FotoUsuari" class="img-circle profile_img">
    </div>
    <div class="profile_info">
        <h2>{{authUser()->nombre}}</h2>

    </div>
    <div class="clearfix"></div>
</div>
<!-- /menu profile quick info -->
