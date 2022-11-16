@php $centros = $elemento->centros->count() @endphp
<ul class="messages centro">
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
                                    @if ($centros > 1)
                                        <a href='/instructor/{!!$instructor->dni!!}/copy/{!!$centro->id!!}'><em
                                                    class="fa fa-copy"></em></a>
                                    @endif
                                    <a href='/instructor/{!!$instructor->dni!!}/edit/{!!$centro->id!!}'><em
                                                class="fa fa-edit"></em></a>
                                    <a href="/instructor/{!!$instructor->dni!!}/delete/{!!$centro->id!!}"
                                       class="delGrupo instructor"><em class="fa fa-trash"></em></a>
                                    <abbr title='{{$instructor->email}} ({{$instructor->telefono}})'><em
                                                class="fa fa-user user-profile-icon"></em><span
                                                class='nombre'> {{$instructor->nombre}}</span>({{$instructor->dni}}
                                        )</abbr>
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
                    <a href="/centro/{!!$centro->id!!}/delete" class="delGrupo"><em class="fa fa-trash"></em></a> <a
                            href="/centro/{{$centro->id}}/edit"><em class="fa fa-edit"></em></a>
                </h4>
                <h4>{{$centro->direccion}}, {{$centro->localidad}} <em class="fa fa-map-marker user-profile-icon"></em>
                </h4>
                @if ($centro->horarios)
                    <h4><em class="fa fa-clock-o user-profile-icon"></em> {{$centro->horarios}}</h4>
                @endif
                @if ($centro->observaciones)
                    <blockquote class="message">{{$centro->observaciones}}</blockquote>
                @endif
                <h4>
                    @if  (userIsAllow(config('roles.rol.administrador')) && ($centros>1))
                        <input type="button" id="{{$centro->id}}" class="btn btn-sm btn-danger" value="Crear Empresa" />
                        <small style="color: purple "> Fussionar:</small>
                        <input type="checkbox" value='{!!$centro->id!!}' />
                    @endif
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
    @if  (userIsAllow(config('roles.rol.administrador')))
        <button type="button" class="btn btn-primary" id='fusionar'>
            Fussionar
        </button>
    @endif
</div>
@include('empresa.partials.modalCentro')
@include('layouts.partials.error')
