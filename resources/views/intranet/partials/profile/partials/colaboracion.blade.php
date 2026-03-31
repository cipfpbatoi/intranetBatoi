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
    $preasignacionesCollapseId = 'colaboracion-preasignaciones-' . $elemento->id;
    $preasignacionModalId = 'preasignacion_' . $elemento->id;
    $ultimContacte = $elemento->ultimaActividad ?? null;
    $diesSenseContacte = $elemento->diesSenseContacte;
    $fitxaBadges = $elemento->fitxaBadges ?? collect();
    $relacionadas = $elemento->relacionadas ?? collect();
    $preasignaciones = $elemento->preasignacionesPanel ?? collect();
    $preasignacionAlumnoOptions = $elemento->preasignacionAlumnoOptions ?? collect();
    $canPreassign = isset($pestana) && $pestana->getNombre() === 'colabora';
    $activePreasignacionesCount = $preasignaciones
        ->whereIn('estado', ['proposta', 'reservada'])
        ->count();
    $hasPreasignacionCapacity = $activePreasignacionesCount < max(1, (int) ($elemento->puestos ?? 1));
    $preasignacionBadgeClass = static function (string $estado): string {
        return match ($estado) {
            'reservada' => 'bg-success',
            'convertida' => 'bg-primary',
            'descartada' => 'bg-danger',
            default => 'bg-warning text-dark',
        };
    };
@endphp
<div class="col-md-4 col-sm-4 col-xs-12 profile_details mis-colaboraciones-card"
     data-target-tab="{{ $tabName ?? '' }}"
     data-town="{{ $elemento->localidad }}"
     data-company="{{ $elemento->Centro->nombre }}"
     data-has-contact="{{ trim((string) ($elemento->contacto ?? '')) !== '' ? '1' : '0' }}"
     data-has-phone="{{ trim((string) ($elemento->telefono ?? '')) !== '' ? '1' : '0' }}"
     data-has-email="{{ trim((string) ($elemento->email ?? '')) !== '' ? '1' : '0' }}"
     data-has-instructor="{{ !empty($elemento->hasInstructor) ? '1' : '0' }}"
     data-conveni-pendent="{{ !empty($elemento->conveniPendent) ? '1' : '0' }}"
     data-has-company-document="{{ !empty($elemento->teDocumentEmpresa) ? '1' : '0' }}"
     data-has-fcts="{{ !empty($elemento->fctsAssociadesCount) ? '1' : '0' }}"
     data-days-without-contact="{{ $diesSenseContacte ?? '' }}"
     data-priority-score="{{ (int) ($elemento->prioritatFitxa ?? 0) }}"
     data-preparation-state="{{ $elemento->estatPreparacioKey ?? 'no_preparada' }}"
     data-preparation-rank="{{ (int) ($elemento->estatPreparacioRank ?? 0) }}"
     data-documentation-pending-count="{{ (int) ($elemento->documentacioPendentCount ?? 0) }}"
     data-last-contact-id="{{ $ultimContacte?->id ?? '' }}"
     data-followup-status="{{ $elemento->seguimentEstatKey ?? 'sense_seguiment' }}"
     data-followup-urgency="{{ $elemento->seguimentUrgenciaKey ?? 'cap' }}">
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
                    <span class="badge {{ $estadoBadgeClass }}">
                        {{ $estadoLabel }}
                    </span>
                    <span class="badge {{ $elemento->estatFitxaClass ?? 'bg-secondary' }}">
                        {{ $elemento->estatFitxaLabel ?? 'Fitxa' }}
                    </span>
                    <span class="badge {{ $elemento->seguimentEstatClass ?? 'bg-secondary' }} js-followup-status-badge">
                        {{ $elemento->seguimentEstatLabel ?? 'Sense seguiment' }}
                    </span>
                    @if (!empty($elemento->seguimentUrgenciaLabel))
                        <span class="badge {{ $elemento->seguimentUrgenciaClass ?? 'bg-secondary' }} js-followup-urgency-badge">
                            {{ $elemento->seguimentUrgenciaLabel }}
                        </span>
                    @else
                        <span class="badge js-followup-urgency-badge" style="display:none;"></span>
                    @endif
                </p>

                @if ($fitxaBadges->isNotEmpty())
                    <p class="mb-2">
                        @foreach ($fitxaBadges as $badge)
                            <span class="badge {{ $badge['class'] }}" style="margin-bottom:.25rem;">
                                <em class="fa {{ $badge['icon'] }}"></em> {{ $badge['label'] }}
                            </span>
                        @endforeach
                    </p>
                @endif

                <ul class="list-unstyled">
                    <li><em class="fa fa-user-secret"></em> {{ $elemento->profesor ?: 'Sense tutor assignat' }}</li>
                    <li><em class="fa fa-user"></em> {{$elemento->contacto ?: 'Sense contacte'}}</li>
                    <li><em class="fa fa-phone"></em> {{$elemento->telefono ?: 'Sense telèfon'}}</li>
                    <li><em class="fa fa-envelope"></em> {{$elemento->email ?: 'Sense email'}}</li>
                    <li><em class="fa fa-briefcase"></em> {{ (int) ($elemento->puestos ?? 0) }} lloc(s) · {{ (int) ($elemento->fctsAssociadesCount ?? 0) }} FCT associada(es)</li>
                </ul>

                @if ($ultimContacte)
                    <p class="small text-muted mb-2 js-last-contact-row">
                        <strong>Últim contacte:</strong>
                        <span class="js-last-contact-value">{{ $ultimContacte->created_at?->format('d/m/Y H:i') }}</span>
                        @if ($diesSenseContacte !== null)
                            <span class="js-last-contact-days">· fa {{ $diesSenseContacte }} dia(es)</span>
                        @else
                            <span class="js-last-contact-days"></span>
                        @endif
                    </p>
                @else
                    <p class="small text-danger mb-2 js-last-contact-row">
                        <strong>Últim contacte:</strong>
                        <span class="js-last-contact-value">Sense cap contacte registrat</span>
                        <span class="js-last-contact-days"></span>
                    </p>
                @endif

                @if (!empty($elemento->proximaAccioText))
                    <p class="small text-info mb-2 js-next-step-row">
                        <strong>Pròxim pas:</strong>
                        <span class="js-next-step-value">{{ $elemento->proximaAccioText }}</span>
                        @if (!empty($elemento->proximaAccioData))
                            <span class="js-next-step-date">· {{ $elemento->proximaAccioData }}</span>
                        @else
                            <span class="js-next-step-date"></span>
                        @endif
                    </p>
                @else
                    <p class="small text-info mb-2 js-next-step-row" style="display:none;">
                        <strong>Pròxim pas:</strong>
                        <span class="js-next-step-value"></span>
                        <span class="js-next-step-date"></span>
                    </p>
                @endif

                <p class="mb-3">
                    <button class="btn btn-default btn-xs" type="button" data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}" aria-expanded="false"
                            aria-controls="{{ $collapseId }}">
                        Més detall
                    </button>
                    @if ($preasignaciones->isNotEmpty() || $canPreassign)
                        <button class="btn btn-default btn-xs" type="button" data-bs-toggle="collapse"
                                data-bs-target="#{{ $preasignacionesCollapseId }}" aria-expanded="false"
                                aria-controls="{{ $preasignacionesCollapseId }}">
                            Reserves ({{ $activePreasignacionesCount }}/{{ max(1, (int) ($elemento->puestos ?? 1)) }})
                        </button>
                    @endif
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
                        <li>
                            <em class="fa fa-check-square-o"></em>
                            Preparació: <strong>{{ $elemento->estatPreparacioLabel ?? 'Sense valorar' }}</strong>
                        </li>
                        <li>
                            <em class="fa fa-files-o"></em>
                            Estat documental:
                            @if (($elemento->documentacioPendentItems ?? collect())->isNotEmpty())
                                <strong>{{ ($elemento->documentacioPendentItems ?? collect())->count() }} pendent(s)</strong>
                            @else
                                <strong>Al dia</strong>
                            @endif
                        </li>
                        <li>
                            <em class="fa fa-sitemap"></em>
                            FCT associades: {{ (int) ($elemento->fctsAssociadesCount ?? 0) }}
                            @if (!empty($elemento->ultimaFctId))
                                · última #{{ $elemento->ultimaFctId }}
                            @endif
                        </li>
                        @if (($elemento->documentacioPendentItems ?? collect())->isNotEmpty())
                            <li>
                                <em class="fa fa-angle-right"></em>
                                {{ ($elemento->documentacioPendentItems ?? collect())->join(' · ') }}
                            </li>
                        @endif
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
                        <li><em class="fa fa-calendar"></em> Annex I: {{ $elemento->annexIData ?: 'Sense data' }}</li>
                        <li><em class="fa fa-clock-o"></em> {{$elemento->Centro->horarios ?: 'Sense horari'}}</li>
                        <li><em class="fa fa-map-marker"></em> {{$elemento->Centro->direccion ?: 'Sense adreça'}}</li>
                        <li><em class="fa fa-file-pdf-o"></em> Document empresa: {{ !empty($elemento->teDocumentEmpresa) ? 'Disponible' : 'Pendent' }}</li>
                @else
                        <li><em class="fa fa-clock-o"></em> {{$elemento->Centro->horarios}}</li>
                        <li><em class="fa fa-map-marker"></em> {{$elemento->Centro->direccion}}</li>
                        <li><em class="fa fa-folder"></em> {{$elemento->Centro->Empresa->actividad}}</li>
                        @if (($elemento->Centro->Empresa->email ?? '') !== ($elemento->email ?? ''))
                            <li><em class="fa fa-envelope"></em> {{$elemento->Centro->Empresa->email}}</li>
                        @endif
                        <li><em class="fa fa-calendar"></em> Annex I: {{ $elemento->annexIData ?: 'Sense data' }}</li>
                        <li><em class="fa fa-file-pdf-o"></em> Document empresa: {{ !empty($elemento->teDocumentEmpresa) ? 'Disponible' : 'Pendent' }}</li>
                @endisset
                    </ul>
                    <p class="mb-0" style="margin-top:.75rem">
                        <a href="{{ route('empresa.detalle', ['empresa' => $elemento->Centro->idEmpresa]) }}" class="btn btn-default btn-xs">
                            <em class="fa fa-building"></em> Empresa
                        </a>
                        @if (!empty($elemento->teDocumentEmpresa))
                            <a href="{{ route('empresa.document', ['empresa' => $elemento->Centro->idEmpresa]) }}"
                               class="btn btn-default btn-xs"
                               target="_blank">
                                <em class="fa fa-file-pdf-o"></em> Document
                            </a>
                        @endif
                        @if (!empty($elemento->ultimaFctId))
                            <a href="{{ route('fct.show', ['id' => $elemento->ultimaFctId]) }}" class="btn btn-default btn-xs">
                                <em class="fa fa-graduation-cap"></em> Última FCT
                            </a>
                        @endif
                    </p>
                </div>

                <div class="collapse" id="{{ $preasignacionesCollapseId }}">
                    @if ($preasignaciones->isEmpty())
                        <p class="small text-muted">Encara no hi ha cap alumne preassignat.</p>
                    @else
                        <ul class="list-unstyled">
                            @foreach ($preasignaciones as $preasignacion)
                                <li class="preasignacion-item" style="margin-bottom:.5rem">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <strong>{{ optional($preasignacion->Alumno)->fullName ?? $preasignacion->idAlumno }}</strong>
                                            <span class="badge {{ $preasignacionBadgeClass((string) $preasignacion->estado) }}">
                                                {{ ucfirst((string) $preasignacion->estado) }}
                                            </span>
                                            <div class="text-muted">
                                                {{ data_get($preasignacion, 'Alumno.Grupo.0.codigo', 'Sense grup') }}
                                                ·
                                                {{ optional($preasignacion->Profesor)->shortName ?? $preasignacion->idProfesor }}
                                            </div>
                                            @if (optional($preasignacion->Alumno)->nia)
                                                <div>
                                                    <a href="{{ route('alumno.days', ['id' => $preasignacion->Alumno->nia]) }}"
                                                       class="preasignacion-horari-link">
                                                        <em class="fa fa-calendar"></em> Horari
                                                    </a>
                                                </div>
                                            @endif
                                            @if (!empty($preasignacion->observaciones))
                                                <div>{{ $preasignacion->observaciones }}</div>
                                            @endif
                                        </div>
                                        @if (in_array((string) $preasignacion->estado, ['proposta', 'reservada'], true))
                                            <a href="{{ route('colaboracion.preasignacion.descartar', ['id' => $preasignacion->id]) }}"
                                               class="btn btn-default btn-xs"
                                               onclick="return confirm('Segur que vols descartar esta preassignació?');">
                                                <em class="fa fa-times"></em>
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
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
                    <p class="small text-muted js-empty-activity">Sense activitat recent</p>
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
                        @if ($canPreassign)
                            <button type="button"
                                    class="btn btn-info btn-xs"
                                    data-bs-toggle="modal"
                                    data-bs-target="#{{ $preasignacionModalId }}"
                                    @disabled($preasignacionAlumnoOptions->isEmpty() || !$hasPreasignacionCapacity)
                                    title="{{ !$hasPreasignacionCapacity ? 'Ja no queden llocs lliures en esta col·laboració' : ($preasignacionAlumnoOptions->isEmpty() ? 'No hi ha alumnat disponible per a este cicle' : 'Reservar alumnat') }}"
                                    style="{{ $hasPreasignacionCapacity ? '' : 'display:none;' }}">
                                <em class="fa fa-user-plus"></em> Reservar
                            </button>
                        @endif
                        <x-botones :panel="$panel" tipo="profile" :elemento="$elemento ?? null" :centrado="false" />
                        <x-botones :panel="$panel" tipo="nofct" :elemento="$elemento ?? null" :centrado="false" />
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>

@if ($canPreassign)
    <x-modal name="{{ $preasignacionModalId }}"
             title="Reservar alumnat"
             action="{{ route('colaboracion.preasignacion.store', ['colaboracion' => $elemento->id]) }}"
             message="{{ $preasignacionAlumnoOptions->isEmpty() || !$hasPreasignacionCapacity ? '' : 'Guardar' }}">
        <input type="hidden" name="estado" value="proposta">

        @if (!$hasPreasignacionCapacity)
            <p class="text-muted mb-0">Ja no queden llocs lliures per a reservar més alumnat.</p>
        @elseif ($preasignacionAlumnoOptions->isEmpty())
            <p class="text-muted mb-0">No hi ha alumnat disponible del teu grup de tutoria per a este cicle.</p>
        @else
            <div class="form-group">
                <label for="preasignacion-alumno-{{ $elemento->id }}">Alumne</label>
                <select id="preasignacion-alumno-{{ $elemento->id }}" name="idAlumno" class="form-control" required>
                    <option value="">Selecciona alumnat del teu grup</option>
                    @foreach ($preasignacionAlumnoOptions as $nia => $label)
                        <option value="{{ $nia }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="preasignacion-observaciones-{{ $elemento->id }}">Observacions</label>
                <textarea id="preasignacion-observaciones-{{ $elemento->id }}"
                          name="observaciones"
                          class="form-control"
                          rows="3"></textarea>
            </div>
        @endif
    </x-modal>
@endif
