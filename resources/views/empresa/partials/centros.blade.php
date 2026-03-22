@include('empresa.partials.instructores')
<ul class="nav navbar-right panel_toolbox">
    <li>
        <a class="addCol"
           href="{{$centro->id}}"
           data-bs-toggle="modal"
           data-bs-target="#AddColaboration"
        >
            <em class="fa fa-plus-square-o"> Nova Col·laboració</em>
        </a>
    </li>
</ul>
<h2><em class="fa fa-graduation-cap"></em> Col·laboracions</h2>
@foreach ($centro->Colaboraciones as $colaboracion)
    <div class="accordion" id="accordion{{$colaboracion->id}}">
        <div class="card">
            <ul class="nav navbar-right panel_toolbox">
                @if ($misColaboracionesIds->contains($colaboracion->id))
                    <li>
                        <a href="{{ route('colaboracion.destroy', ['colaboracion' => $colaboracion->id]) }}">
                            <em class="fa fa-trash"></em>
                        </a>
                    </li>
                @endif
                @if (in_array($colaboracion->idCiclo, $ciclosDepartamentoIds, true))
                    <li>
                        <a class='editar' id="{{$colaboracion->id}}" href="{{ route('colaboracion.edit', ['colaboracion' => $colaboracion->id]) }}">
                            <em class="fa fa-edit"></em>
                        </a>
                    </li>
                @endif
                @if (!$existeColaboracion)
                    <li>
                        <a href="{{ route('colaboracion.copy', ['colaboracion' => $colaboracion->id]) }}" class="copGrupo">
                            <em class="fa fa-copy">Duplicar</em>
                        </a>
                    </li>
                @endif
            </ul>
            <a class="d-block collapsed"
               id="headingOne{{$colaboracion->id}}"
               data-bs-toggle="collapse"
               data-bs-parent="#accordion{{$colaboracion->id}}"
               href="#collapseOne{{$colaboracion->id}}"
               aria-expanded="false"
               aria-controls="collapseOne{{$colaboracion->id}}">
                @if ($colaboracion->idCiclo == $cicloTutoria)
                    <h4 class="card-title">
                        {{ $colaboracion->ciclo->ciclo }}
                        <em class="fa fa-group user-profile-icon"></em> {{$colaboracion->puestos}}
                    </h4>
                @else
                    <h4 style="color:darkgrey" class="card-title">
                        {{ strtolower($colaboracion->ciclo->ciclo) }}
                        <em class="fa fa-group user-profile-icon"></em> {{$colaboracion->puestos}}
                    </h4>
                @endif
            </a>
            <div id="collapseOne{{$colaboracion->id}}"
                 class="collapse"
                 aria-labelledby="headingOne{{$colaboracion->id}}">
                <div class="card-body">
                    <em class="fa fa-user user-profile-icon"></em> {!! $colaboracion->contacto !!}
                    <em class="fa fa-phone user-profile-icon"></em> {{$colaboracion->telefono}}
                    <em class="fa fa-envelope user-profile-icon"></em> {{$colaboracion->email}}
                </div>
            </div>
        </div>
    </div>
@endforeach
