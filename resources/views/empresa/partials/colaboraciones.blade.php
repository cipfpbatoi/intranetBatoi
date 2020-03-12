@php
     $tutor = \Intranet\Entities\Grupo::find(AuthUser()->GrupoTutoria);
     if (!$tutor) $tutor = \Intranet\Entities\Grupo::QTutor(null,true)->first();
@endphp
<ul class="messages colaboracion">
        @foreach ($elemento->colaboraciones as $colaboracion)
            @php $editar = $misColaboraciones->contains($colaboracion->id); @endphp
        <li>
            <div class="message_date" style="width:50%">
                <h4>
                    <span class='info' style="font-weight: bold">{!! $colaboracion->Centro->nombre !!}</span><span class='info' style="font-weight:normal "> ({!! $colaboracion->Centro->direccion  !!})</span> - <span class='info' style="font-weight: bold">{!! $colaboracion->Ciclo->ciclo !!}</span><sup>{{$colaboracion->fcts()->count()}}<small style="color: purple "> Fct</small></sup>
                    @if ($editar)
                        <a href='/colaboracion/{!!$colaboracion->id!!}/edit'><i class="fa fa-edit" title='Modificar col.laboració'></i></a> 
                        <a href="/colaboracion/{!!$colaboracion->id!!}/delete" class="delGrupo"><i class="fa fa-trash" title='Esborrar col.laboració'></i></a>
                    @endif
                    @if ($misColaboraciones->where('idCentro',$colaboracion->idCentro)->where('idCiclo',$tutor->idCiclo)->count() == 0) <a href="/colaboracion/{!!$colaboracion->id!!}/copy" class="copGrupo"><i class="fa fa-copy"></i></a><small style="color: purple "> @lang('messages.buttons.copy')  {{$tutor->Ciclo->ciclo}} </small>@endif
                    <br/>
                    @if (count($colaboracion->votes))
                        <a href="/votes/{{$colaboracion->id}}/show">
                            <i class="fa fa-bar-chart"></i>Poll
                        </a>
                    @endif
                    
                </h4>
            </div>
            <div class="message_wrapper" style="width:50%">
                <h4><i class="fa fa-user user-profile-icon"> </i> {!! $colaboracion->contacto !!} <i class="fa fa-phone user-profile-icon"></i> {{$colaboracion->telefono}}</h4>
                <h4><i class="fa fa-envelope user-profile-icon"></i>{{$colaboracion->email}}</h4>
                <h4 class="text-info">{{isset($coraboracion->propietario->fullName)?$colaboracion->propietario->fullName:$colaboracion->tutor}} <i class="fa fa-group user-profile-icon"></i> {{$colaboracion->puestos}}</h4>
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



