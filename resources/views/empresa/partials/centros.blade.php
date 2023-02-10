@include('empresa.partials.instructores')
<ul class="nav navbar-right panel_toolbox">
    <li>
        <a class="addCol panel-heading collapsed"
           href="{{$centro->id}}"
           data-toggle="modal"
           data-target="#AddColaboration"
        >
            <em class="fa fa-plus-square-o"> Nova Col·laboració</em>
        </a>
    </li>
</ul>
<h2><em class="fa fa-graduation-cap"></em> Col·laboracions</h2>
@foreach ($centro->Colaboraciones as $colaboracion)
    <div class="accordion"  id="accordion{{$colaboracion->id}}" role="tablist" aria-multiselectable="true">
        <div class="panel">
            <ul class="nav navbar-right panel_toolbox">
                @if ($misColaboraciones->contains($colaboracion->id))
                    <li>
                        <a href="/colaboracion/{!!$colaboracion->id!!}/delete">
                            <em class="fa fa-trash"></em>
                        </a>
                    </li>
                @endif
                @if ( $cicloEsDepartamento =
                    \Intranet\Entities\Ciclo::where('departamento',authUser()->departamento)
                        ->where('id',$colaboracion->idCiclo)
                        ->count()
                )
                    <li>
                        <a class='editar' id="{{$colaboracion->id}}" href="/colaboracion/{!!$colaboracion->id!!}/edit">
                            <em class="fa fa-edit"></em>
                        </a>
                    </li>
                @endif
                @if (!$existeColaboracion)
                    <li>
                        <a href="/colaboracion/{!!$colaboracion->id!!}/copy" class="copGrupo">
                            <em class="fa fa-copy">Duplicar</em>
                        </a>
                    </li>
                @endif
            </ul>
            <a class="panel-heading collapsed"
               role="tab"
               id="headingOne{{$colaboracion->id}}"
               data-toggle="collapse"
               data-parent="#accordion{{$colaboracion->id}}"
               href="#collapseOne{{$colaboracion->id}}"
               aria-expanded="true"
               aria-controls="collapseOne"
            >
                @if ($colaboracion->idCiclo == $ciclo)
                    <h4 class="panel-title">
                        {{ $colaboracion->ciclo->ciclo }}
                        <em class="fa fa-group user-profile-icon"></em> {{$colaboracion->puestos}}
                    </h4>
                @else
                    <h4 style="color:darkgrey" class="panel-title" >
                        {{ strtolower($colaboracion->ciclo->ciclo) }}
                        <em class="fa fa-group user-profile-icon"></em> {{$colaboracion->puestos}}
                    </h4>
                @endif
            </a>
            <div id="collapseOne{{$colaboracion->id}}"
                 class="panel-collapse collapse"
                 role="tabpanel"
                 aria-labelledby="headingOne"
            >
                <div class="panel-body">
                    <em class="fa fa-user user-profile-icon"></em> {!! $colaboracion->contacto !!}
                    <em class="fa fa-phone user-profile-icon"></em> {{$colaboracion->telefono}}
                    <em class="fa fa-envelope user-profile-icon"></em> {{$colaboracion->email}}
                </div>
            </div>
        </div>
    </div>
@endforeach


@if  (userIsAllow(config('roles.rol.administrador')))
    @include('empresa.partials.modalEmpresa')
@endif
