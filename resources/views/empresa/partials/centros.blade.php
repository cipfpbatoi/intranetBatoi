<ul class="messages centro">
    @foreach ($elemento->centros as $centro)
    <li>
        <div class="message_date" style="width:55%">
            <a href='/instructor/{!!$centro->id!!}/create'>Nou Instructor</a>
            @foreach ($centro->instructores as $instructor)
            <h4 class="text-info">
                @if ($centro->Empresa->centros->count() > 1)<a href='/instructor/{!!$instructor->dni!!}/copy/{!!$centro->id!!}'><i class="fa fa-copy"></i></a> @endif
                <a href='/instructor/{!!$instructor->dni!!}/edit/{!!$centro->id!!}'><i class="fa fa-edit"></i></a> 
                <a href="/instructor/{!!$instructor->dni!!}/delete/{!!$centro->id!!}" class="delGrupo instructor"><i class="fa fa-trash"></i></a>
                <acronym title='{{$instructor->email}} ({{$instructor->telefono}})'><i class="fa fa-user user-profile-icon"></i><span class='nombre'> {{$instructor->nombre}}</span>({{$instructor->dni}})</acronym>
            </h4>
            @endforeach
        </div>
        <div class="message_wrapper" style="width:45%">
            <h4>
                <span class="info">{{$centro->nombre}}</span><sup>{{$centro->colaboraciones()->count()}}<small style="color: purple "> Col</small></sup>
                <a href="/centro/{!!$centro->id!!}/delete" class="delGrupo"><i class="fa fa-trash"></i></a> <a href="/centro/{{$centro->id}}/edit"><i class="fa fa-edit"></i></a>
            </h4>
            <h4>
                <a href='#' onclick="window.open('/centro/{{$centro->id}}/mapa','targetWindow','toolbar=no,location=no,status=no, menubar=no, scrollbars=yes,resizable=yes,width=550,height=550');return false;" >
                {{$centro->direccion}}, {{$centro->localidad}} <i class="fa fa-map-marker user-profile-icon"></i></a></h4>
                @if ($centro->horarios) <h4><i class="fa fa-clock-o user-profile-icon"></i> {{$centro->horarios}}</h4> @endif       
                @if ($centro->observaciones) <blockquote class="message">{{$centro->observaciones}}</blockquote> @endif
            <h4>
                @if  (UserisAllow(config('roles.rol.administrador'))&& ($centro->Empresa->centros->count()>1)) <small style="color: purple "> Fussionar:</small> <input type="checkbox" value='{!!$centro->id!!}'> @endif
            </h4>
        </div>
    </li>
    @endforeach
</ul> 
<div class="message_wrapper">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddCenter">
        @lang("messages.generic.anadir") @lang("models.modelos.Centro")
    </button>
    @if  (UserisAllow(config('roles.rol.administrador')))
    <button type="button" class="btn btn-primary" id='fusionar'>
        Fussionar
    </button>
    @endif
</div>
@include('empresa.partials.modalCentro')
@include('layouts.partials.error')