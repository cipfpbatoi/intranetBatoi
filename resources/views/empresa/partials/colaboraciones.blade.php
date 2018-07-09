@php $ciclos = \Intranet\Entities\Ciclo::where('departamento',AuthUser()->departamento)->get() @endphp
<ul class="messages colaboracion">
    @foreach ($elemento->centros as $centro)
        @foreach ($centro->colaboraciones as $colaboracion)
            @php $editar = $ciclos->contains($colaboracion->idCiclo) @endphp
        <li>
            <div class="message_date" style="width:50%">
                <h4>
                    <span class='info' style="font-weight: bold">{!! $colaboracion->Ciclo->ciclo !!} - {!! $colaboracion->Centro->nombre !!} ({!! $colaboracion->Centro->direccion !!})</span><sup>{{$colaboracion->fcts()->count()}}<small style="color: purple "> Fct</small></sup>
                    @if ($editar || UserisAllow(config('roles.rol.administrador'))) <a href='/colaboracion/{!!$colaboracion->id!!}/edit'><i class="fa fa-edit"></i></a> @endif
                    @if ($editar || UserisAllow(config('roles.rol.administrador'))) <a href="/colaboracion/{!!$colaboracion->id!!}/delete" class="delGrupo"><i class="fa fa-trash"></i></a>@endif
                    @if (\Intranet\Entities\Colaboracion::where('idCentro',$colaboracion->idCentro)->where('idCiclo',\Intranet\Entities\Grupo::QTutor()->first()->idCiclo)->count() == 0) <a href="/colaboracion/{!!$colaboracion->id!!}/copy" class="copGrupo"><i class="fa fa-copy"></i></a><small style="color: purple "> @lang('messages.buttons.copy')  {{\Intranet\Entities\Grupo::QTutor()->first()->Ciclo->ciclo}} </small>@endif
                </h4>
            </div>
            <div class="message_wrapper" style="width:50%">
                <h4><i class="fa fa-user user-profile-icon"> </i> {!! $colaboracion->contacto !!} <i class="fa fa-phone user-profile-icon"></i> {{$colaboracion->telefono}}</h4>
                <h4><i class="fa fa-envelope user-profile-icon"></i>{{$colaboracion->email}}</h4>
                <h4 class="text-info">{{$colaboracion->tutor}} <i class="fa fa-group user-profile-icon"></i> {{$colaboracion->puestos}}</h4>
            </div>
        </li>    
        @endforeach
    @endforeach
</ul>
<div class="message_wrapper">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddColaboration">
        @lang("messages.generic.anadir") @lang("models.modelos.Colaboracion")
    </button>
</div>
@include('empresa.partials.modalColaboraciones')
@include('layouts.partials.error')



