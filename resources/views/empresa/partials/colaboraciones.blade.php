@php
    $tutor = \Intranet\Entities\Grupo::find(authUser()->GrupoTutoria);
    if (!$tutor) $tutor = \Intranet\Entities\Grupo::QTutor(null,true)->first();
    $grupoDual = \Intranet\Entities\Grupo::where('tutorDual',authUser()->dni)->first();
    $cicloDual =  $grupoDual ? $grupoDual->Ciclo->id :null;
@endphp
<ul class="messages colaboracion">
    @foreach ($elemento->colaboraciones as $colaboracion)
        @php $editar = $misColaboraciones->contains($colaboracion->id);
                 $dual = ($cicloDual == $colaboracion->Ciclo->id) ? 1 : 0;
        @endphp
        <li>
            <div class="message_date" style="width:50%">
                <h4>
                    <span class='info' style="font-weight: bold">{!! $colaboracion->Centro->nombre !!}</span>
                    <span class='info' style="font-weight:normal ">({!! $colaboracion->Centro->direccion  !!})</span>
                    - <span class='info' style="font-weight: bold">{!! $colaboracion->Ciclo->ciclo !!}</span>
                    <sup>{{$colaboracion->fcts()->count()}}
                        <small style="color: purple "> Fct</small>
                    </sup>
                    @if ($editar)
                        <a class='editar' id='{{$colaboracion->id}}' href='/colaboracion/{!!$colaboracion->id!!}/edit'>
                            <em class="fa fa-edit" title='Modificar col.laboració'></em>
                        </a>
                        <a href="/colaboracion/{!!$colaboracion->id!!}/delete" class="delGrupo">
                            <em class="fa fa-trash" title='Esborrar col.laboració'></em>
                        </a>
                    @endif

                    @if (
                        count($misColaboraciones) &&
                        $misColaboraciones->where('idCentro',$colaboracion->idCentro)
                        ->where('idCiclo',$tutor->idCiclo)
                        ->count() == 0
                        )
                        <a href="/colaboracion/{!!$colaboracion->id!!}/copy" class="copGrupo">
                            <em class="fa fa-copy"></em>
                        </a>
                        <small style="color: purple "> @lang('messages.buttons.copy')  {{$tutor->Ciclo->ciclo}} </small>
                    @endif
                    <br/>
                    @if (count($colaboracion->votes))
                        <a href="/votes/{{$colaboracion->id}}/show">
                            <em class="fa fa-bar-chart"></em>Poll
                        </a>
                    @endif
                    @if ($dual)
                        <a href="/colaboracion/{{$colaboracion->id}}/print">
                            <em class="fa fa-file-zip-o"></em>Dual
                        </a>
                    @endif
                </h4>
            </div>
            <div class="message_wrapper" style="width:50%">
                <h4>
                    <em class="fa fa-user user-profile-icon"></em> {!! $colaboracion->contacto !!}
                    <em class="fa fa-phone user-profile-icon"></em> {{$colaboracion->telefono}}
                </h4>
                <h4>
                    <em class="fa fa-envelope user-profile-icon"></em>{{$colaboracion->email}}
                </h4>
                <h4 class="text-info">
                    {{  isset($coraboracion->propietario->fullName)?
                        $colaboracion->propietario->fullName:
                        $colaboracion->tutor
                    }}
                    <em class="fa fa-group user-profile-icon"></em> {{$colaboracion->puestos}}
                </h4>
            </div>
        </li>
    @endforeach

</ul>
<div class="message_wrapper">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddColaboration">
        @lang("messages.generic.anadir") @lang("models.modelos.Colaboracion")
    </button>
</div>
@include('empresa.partials.modalColaboraciones')
@include('layouts.partials.error')



