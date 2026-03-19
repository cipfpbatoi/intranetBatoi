@php
    $estadoBadgeClass = match ((int) ($elemento->estado ?? 0)) {
        2 => 'bg-success',
        3 => 'bg-danger',
        1 => 'bg-warning text-dark',
        default => 'bg-secondary',
    };
    $estadoLabel = match ((int) ($elemento->estado ?? 0)) {
        2 => 'Col·labora',
        3 => 'No col·labora',
        1 => 'Pendent',
        default => 'Sense classificar',
    };
    $collapseId = 'colaboracion-detall-' . $elemento->id;
    $relatedCollapseId = 'colaboracion-relacionades-' . $elemento->id;
    $ultimContacte = $elemento->ultimaActividad ?? null;
    $relacionadas = $elemento->relacionadas ?? collect();
@endphp
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view"
         @if ($elemento->estado == 3) style='border-color: #90111a;border-width: medium' @endif
    >
        <div class="col-sm-12">
            <div class="left col-md-8 col-xs-12">
                <h5>
                     {{$elemento->Centro->nombre}}
                </h5>
                <p class="small text-muted mb-2">
                    <em class="fa fa-map-marker"></em> {{$elemento->localidad}}
                </p>
                <p class="mb-2">
                    <span class="badge {{ $isMine ? 'bg-success' : 'bg-secondary' }}">
                        {{ $isMine ? 'Meua' : 'Altre tutor' }}
                    </span>
                    <span class="badge {{ $estadoBadgeClass }}">
                        {{ $estadoLabel }}
                    </span>
                </p>
                <ul class="list-unstyled">
                    <li><em class="fa fa-user"></em> {{$elemento->contacto ?: 'Sense contacte'}}</li>
                    <li><em class="fa fa-phone"></em> {{$elemento->telefono ?: 'Sense telèfon'}}</li>
                    <li><em class="fa fa-envelope"></em> {{$elemento->email ?: 'Sense email'}}</li>
                </ul>

                @if ($ultimContacte)
                    <p class="small text-muted mb-2">
                        <strong>Últim contacte:</strong>
                        {{ $ultimContacte->created_at?->format('d/m/Y H:i') }}
                    </p>
                @endif

                <p class="mb-3">
                    <button class="btn btn-default btn-xs" type="button" data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}" aria-expanded="false"
                            aria-controls="{{ $collapseId }}">
                        Més detall
                    </button>
                    @if($relacionadas->isNotEmpty())
                        <button class="btn btn-default btn-xs" type="button" data-bs-toggle="collapse"
                                data-bs-target="#{{ $relatedCollapseId }}" aria-expanded="false"
                                aria-controls="{{ $relatedCollapseId }}">
                            Altres cicles ({{ $relacionadas->count() }})
                        </button>
                    @endif
                </p>

                <div class="collapse" id="{{ $collapseId }}">
                    <ul class="list-unstyled">
                @isset (authUser()->emailItaca)
                        <li>Conveni: <strong>
                                {{$elemento->Centro->Empresa->concierto}}
                                @if ($elemento->Centro->Empresa->conveniCaducat)
                                    <em class="fa fa-hand-o-down"></em>
                                @else
                                    <em class="fa fa-hand-o-up"></em>
                                @endif
                            </strong>
                        </li>
                        <li><em class="fa fa-group"></em> {{$elemento->puestos}} lloc(s) de treball</li>
                        <li><em class="fa fa-user-secret"></em> {{$elemento->profesor ?? 'No assignada'}}</li>
                @else
                        <li><em class="fa fa-group"></em> {{$elemento->puestos}} lloc(s) de treball</li>
                        <li><em class="fa fa-clock-o"></em> {{$elemento->Centro->horarios}}</li>
                        <li><em class="fa fa-map-marker"></em> {{$elemento->Centro->direccion}}</li>
                        <li><em class="fa fa-folder"></em> {{$elemento->Centro->Empresa->actividad}}</li>
                        <li><em class="fa fa-envelope"></em> {{$elemento->Centro->Empresa->email}}</li>
                @endisset
                    </ul>
                </div>
            </div>

            <div class="col-md-4 col-xs-12 listActivity">
                <p class="small text-muted mb-1">
                    <strong>Activitat recent</strong>
                </p>
                @forelse ($contactos as $contacto)
                    <x-activity :activity="$contacto" />
                    <br/>
                @empty
                    <p class="small text-muted">Sense activitat recent</p>
                @endforelse
            </div>
            @if($relacionadas->isNotEmpty())
                <div class="col-md-12 col-xs-12 collapse" id="{{ $relatedCollapseId }}" style="margin-top:.5rem">
                    <ul class="list-unstyled">
                        <li><span class="badge bg-secondary">Altres cicles (mateix centre/departament)</span></li>

                    @foreach ($relacionadas as $rel)
                        <li class="small" style="margin-top:.25rem">
                            <em class="fa fa-institution"></em>
                            {{ optional($rel->Ciclo)->literal ?? ($rel->idCiclo ?? $rel->ciclo_id) }}
                            — {{ optional($rel->Propietario)->shortName }}

                            @if(($rel->contactos ?? collect())->isNotEmpty())
                                @if ($rel->estado == 3)
                                    <span class="badge bg-danger">Contactada</span>
                                @endif
                                @if ($rel->estado == 2)
                                    <span class="badge bg-success">Contactada</span>
                                @endif
                                @if ($rel->estado == 1)
                                    <span class="badge bg-warning text-dark">Contactada</span>
                                @endif
                                <div class="mt-1">
                                    @foreach ($rel->contactos as $act)
                                        @if ($act->document === "Contacte previ")
                                            <div class="text-muted"
                                                 data-bs-toggle="tooltip"
                                                 data-bs-placement="top"
                                                 title=" {{ $act->comentari }}"
                                                 style="cursor:pointer;" >
                                                <em class="fa fa-commenting"></em>
                                                {{ $act->created_at?->format('d/m/Y H:i') }} 
                                                
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                    </ul>
                </div>
            @endif
           
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 emphasis">
                @isset (authUser()->emailItaca)
                    <div class="d-flex flex-wrap justify-content-center gap-1">
                        <x-botones :panel="$panel" tipo="profile" :elemento="$elemento ?? null" :centrado="false" />
                        <x-botones :panel="$panel" tipo="nofct" :elemento="$elemento ?? null" :centrado="false" />
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>
