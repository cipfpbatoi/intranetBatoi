@php $centros = $elemento->centros->count() @endphp
<ul class="messages centro" >
    @foreach ($elemento->centros as $centro)
    <li style="clear: both">
        <div class="message_date" style="width:55%">
            @if (config('variables.altaInstructores') || $misColaboraciones->where('idCentro',$centro->id)->count())
                <a href='/instructor/{!!$centro->id!!}/create'>Nou Instructor</a>
                @foreach ($centro->instructores->sortBy('departamento')->groupBy('departamento') as $departament)
                    <div>
                        <h6>{{$departament->first()->departamento}}</h6>
                    @foreach($departament->sortBy('surnames') as $instructor)
                            <h4 class="text-info">
                                @if ($centros > 1)<a href='/instructor/{!!$instructor->dni!!}/copy/{!!$centro->id!!}'><i class="fa fa-copy"></i></a> @endif
                                <a href='/instructor/{!!$instructor->dni!!}/edit/{!!$centro->id!!}'><i class="fa fa-edit"></i></a>
                                <a href="/instructor/{!!$instructor->dni!!}/delete/{!!$centro->id!!}" class="delGrupo instructor"><i class="fa fa-trash"></i></a>
                                <acronym title='{{$instructor->email}} ({{$instructor->telefono}})'><i class="fa fa-user user-profile-icon"></i><span class='nombre'> {{$instructor->nombre}}</span>({{$instructor->dni}})</acronym>
                            </h4>
                    @endforeach
                        <hr/>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="message_wrapper" style="width:45%">
            <h4>
                <span class="info">{{$centro->nombre}}</span>
                <a href="/centro/{!!$centro->id!!}/delete" class="delGrupo"><i class="fa fa-trash"></i></a> <a href="/centro/{{$centro->id}}/edit"><i class="fa fa-edit"></i></a>
            </h4>
            <h4>{{$centro->direccion}}, {{$centro->localidad}} <i class="fa fa-map-marker user-profile-icon"></i></h4>
            @if ($centro->horarios) <h4><i class="fa fa-clock-o user-profile-icon"></i> {{$centro->horarios}}</h4> @endif       
            @if ($centro->observaciones) <blockquote class="message">{{$centro->observaciones}}</blockquote> @endif
            <h4>
                @if  (UserisAllow(config('roles.rol.administrador'))&& ($centros>1)) <small style="color: purple "> Fussionar:</small> <input type="checkbox" value='{!!$centro->id!!}'> @endif
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