@if (AuthUser()->rol % config('constants.rol.alumno') == 0)
    <div class="navbar nav_title" style="border: 0;">
        <a href="/alumno/home" class="site_title"><i class="fa fa-paw"></i> <span>{!! config('contacto.titulo') !!}</span></a>
    </div>
@else
    <div class="navbar nav_title" style="border: 0;">
        <a href="/home" class="site_title"><i class="fa fa-paw"></i> <span>{!! config('contacto.titulo') !!}</span></a>
    </div> 
@endif


<div class="clearfix"></div>

<!-- menu profile quick info -->
<div class="profile clearfix">
    <div id="dni" class="hidden">{!! AuthUser()->dni !!}</div>
    <div id="rol" class="hidden">{!! AuthUser()->rol !!}</div>
    @if (isset(AuthUser()->api_token))
    <div id="_token" class="hidden">{!! AuthUser()->api_token !!}</div>
    @endif
    <div class="profile_pic">
        <img src="{{ asset(AuthUser()->foto) }}" alt="FotoUsuari" class="img-circle profile_img">
    </div>
    <div class="profile_info">
        <h2>{{AuthUser()->nombre}}</h2>
        
    </div>
    <div class="clearfix"></div>
</div>
<!-- /menu profile quick info -->
