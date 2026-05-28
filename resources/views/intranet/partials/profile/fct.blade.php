@once
    @push('styles')
        <style>
            .fct-cards-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 16px;
                width: 100%;
            }

            @media (min-width: 992px) {
                .fct-cards-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (min-width: 1700px) {
                .fct-cards-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            @media (min-width: 2400px) {
                .fct-cards-grid {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }
            }

            .fct-cards-grid .fct-card {
                min-width: 0;
                width: 100%;
            }

            .fct-card .profile_view {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .fct-card .profile_view > .fct,
            .fct-card .profile_view > .bottom,
            .fct-card .profile_view > .bottom > .emphasis {
                width: 100%;
                float: none;
            }

            .fct-card .profile_view > .fct {
                display: flex;
                flex-direction: column;
                flex: 1 1 auto;
            }

            .fct-card .profile_view .left,
            .fct-card .profile_view .listActivity,
            .fct-card .profile_view .studentActivity {
                width: 100%;
                float: none;
                padding-left: 0;
                padding-right: 0;
            }

            .fct-card .profile_view .listActivity,
            .fct-card .profile_view .studentActivity {
                margin-top: 12px;
            }

            .fct-card .fct-operational-summary {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin: 8px 0 10px;
            }

            .fct-card .fct-operational-summary .label {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                white-space: normal;
                line-height: 1.35;
            }

            .fct-card .fct-contact-links {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 8px;
            }

            .fct-card .fct-contact-links .btn {
                margin: 0;
            }

            .fct-card .fct-card-title {
                display: flex;
                align-items: center;
                gap: 8px;
                justify-content: flex-start;
                margin-top: 0;
            }

            .fct-card .fct-card-title .btn {
                flex: 0 0 auto;
            }

            .fct-card .fct-warning-list {
                margin: 6px 0 0;
                color: #8a6d3b;
            }

            @media (min-width: 1800px) {
                .fct-card .profile_view > .fct {
                    flex-direction: row;
                    flex-wrap: wrap;
                    gap: 16px;
                }

                .fct-card .profile_view .left {
                    width: calc(42% - 11px);
                }

                .fct-card .profile_view .listActivity,
                .fct-card .profile_view .studentActivity {
                    width: calc(29% - 11px);
                    margin-top: 0;
                }
            }
        </style>
    @endpush
@endonce

<div class="fct-cards-grid">
    @foreach ($panel->getElementos($pestana) as $fct)
        @php
            $colaboracion = $fct->Colaboracion;
            $centro = $colaboracion?->Centro;
            $empresa = $centro?->Empresa;
            $instructor = $fct->Instructor;
            $contactos = $fct->Contactos ?? collect();
            $lastContacto = $contactos->sortByDesc('created_at')->first();
            $alumnosCount = $fct->relationLoaded('AlFct') ? $fct->AlFct->count() : $fct->Alumnos->count();
            $telefonoInstructor = trim((string) ($instructor->telefono ?? ''));
            $emailInstructor = trim((string) ($instructor->email ?? ''));
            $telefonoContacto = trim((string) ($colaboracion->telefono ?? ''));
            $emailContacto = trim((string) ($colaboracion->email ?? ''));
            $alumnoContactos = $fct->AlFct
                ->flatMap(function ($alumnoFct) {
                    return ($alumnoFct->Contactos ?? collect())->map(function ($contacto) use ($alumnoFct) {
                        $contacto->alumno_short_name = $alumnoFct->Alumno->shortName ?? $alumnoFct->Alumno->fullName ?? '';
                        return $contacto;
                    });
                })
                ->sortBy('created_at')
                ->values();
            $warnings = collect();

            if (!$centro) {
                $warnings->push('sense centre');
            }
            if (!$instructor) {
                $warnings->push('sense instructor');
            } else {
                if ($telefonoInstructor === '') {
                    $warnings->push('instructor sense telèfon');
                }
                if ($emailInstructor === '') {
                    $warnings->push('instructor sense email');
                }
            }
            if (!$empresa?->concierto) {
                $warnings->push('conveni pendent');
            }
        @endphp
        <div class="profile_details fct-card" >
            <div id="fct-card-{{$fct->id}}" class="well profile_view">
                <div id="{{$fct->id}}" class="col-sm-12 fct">
                    <div class="left col-md-6 col-xs-12">
                        <h5 class="fct-card-title">
                            <a href="{{ route('fct.show', ['id' => $fct->id]) }}" class="btn-success btn btn-xs" title="Mostrar Fct">
                                <i class="fa fa-eye"></i>
                            </a>
                            <span>{{ $centro->nombre ?? 'Sense centre' }}</span>
                        </h5>
                        <div class="fct-operational-summary">
                            <span class="label label-success">
                                <i class="fa fa-users"></i> {{ $alumnosCount }} alumnes
                            </span>
                            <span class="label label-{{ $contactos->count() > 0 ? 'info' : 'warning' }}">
                                <i class="fa fa-comments"></i> {{ $contactos->count() }} seguiments
                            </span>
                            <span class="label label-{{ $fct->correoInstructor ? 'success' : 'default' }}">
                                <i class="fa fa-envelope"></i> {{ $fct->correoInstructor ? 'certificat enviat' : 'certificat pendent' }}
                            </span>
                            @if ($fct->asociacion === 3)
                                <span class="label label-warning">
                                    <i class="fa fa-graduation-cap"></i> DUAL
                                </span>
                            @endif
                        </div>
                        <ul class="list-unstyled">
                                @if ($instructor)
                                    <li><strong>{{$instructor->nombre}}</strong></li>
                                    @if ($telefonoInstructor !== '')
                                        <li><i class="fa fa-phone"></i> {{$telefonoInstructor}}</li>
                                    @endif
                                    @if ($emailInstructor !== '')
                                        <li><i class="fa fa-envelope"></i> {{$emailInstructor}}</li>
                                    @endif
                                @else
                                    <li>No hi ha instructor. Cal corregir el problema</li>
                                @endif
                                @if ($lastContacto)
                                    <li class="text-muted">
                                        Últim seguiment: {{ fechaCurta($lastContacto->created_at) }}
                                    </li>
                                @else
                                    <li class="text-warning">Encara no hi ha seguiments registrats</li>
                                @endif
                        </ul>
                        @if ($emailInstructor !== '' || $emailContacto !== '')
                            <div class="fct-contact-links">
                                @if ($emailInstructor !== '')
                                    <a href="{{ route('PanelColaboracion.colaboracion', ['id' => $fct->id, 'documento' => 'finEmpresa']) }}"
                                       class="btn btn-default btn-xs"
                                       title="Enviar correu fi empresa a l'instructor">
                                        <i class="fa fa-envelope"></i> Instructor
                                    </a>
                                @endif
                                @if ($emailContacto !== '' && $emailContacto !== $emailInstructor)
                                    <a href="{{ route('PanelColaboracion.colaboracion', ['id' => $fct->id, 'documento' => 'finCentro']) }}"
                                       class="btn btn-default btn-xs"
                                       title="Enviar correu fi empresa al centre">
                                        <i class="fa fa-envelope"></i> Centre
                                    </a>
                                @endif
                            </div>
                        @endif
                        @if ($warnings->isNotEmpty())
                            <p class="fct-warning-list">
                                <i class="fa fa-exclamation-triangle"></i>
                                Revisar: {{ $warnings->join(' · ') }}
                            </p>
                        @endif
                        @foreach($panel->getBotones('profile') as $button)
                            {!! $button->show($fct) !!}
                        @endforeach
                    </div>
                    @if ($fct->asociacion === 3) <h5>-DUAL-</h5> @endif
                    <div class="col-md-6 listActivity">
                        <h5>Empresa</h5>
                        @isset (authUser()->emailItaca)
                            @forelse ($contactos as $contacto)
                                <x-activity :activity="$contacto" />
                                <br/>
                            @empty
                                <span class="text-muted">Sense evidències de seguiment.</span>
                            @endforelse
                        @endisset
                    </div>
                    <div class="col-md-6 studentActivity">
                        <h5>Alumnat</h5>
                        <div class="studentActivityList">
                            @forelse ($alumnoContactos as $contacto)
                                @php($isInicioPracticas = str_starts_with((string) $contacto->document, 'Informació relativa'))
                                <small>
                                    @if($isInicioPracticas)
                                        {{ fechaCurta($contacto->created_at) }}
                                        <em class="fa fa-flag"></em>
                                        {{ $contacto->alumno_short_name }}
                                    @else
                                        <a href="#" class="small" id="{{ $contacto->id }}" title="{{ $contacto->comentari }}">
                                            @if($contacto->comentari)
                                                <em class="fa fa-plus"></em>
                                            @else
                                                <em class="fa fa-minus"></em>
                                            @endif
                                            {{ fechaCurta($contacto->created_at) }}
                                            {{ $contacto->alumno_short_name }}
                                        </a>
                                    @endif
                                </small><br/>
                            @empty
                                <span class="text-muted">Sense contactes amb alumnat.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 bottom text-center"
                     @if ($fct->asociacion === 3) style="background-color:orange"@endif
                >
                    <div class="col-xs-12 col-sm-5 emphasis">
                        <p class="ratings">
                            {{ strtoupper($centro->localidad ?? '') }}
                            @foreach($panel->getBotones('fct') as $button)
                                {!! $button->show($fct) !!}
                            @endforeach
                            <a href="#"
                               class="btn btn-primary btn-xs alumnat"
                               data-fct-id="{{ $fct->id }}"
                               title="Registrar contacte amb alumnat">
                                <i class="fa fa-user"></i>
                            </a>
                        </p>
                    </div>
                    <div class="col-xs-12 col-sm-7 emphasis">
                     </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
