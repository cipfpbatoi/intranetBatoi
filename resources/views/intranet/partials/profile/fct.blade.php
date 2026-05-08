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
            .fct-card .profile_view .listActivity {
                width: 100%;
                float: none;
                padding-left: 0;
                padding-right: 0;
            }

            .fct-card .profile_view .listActivity {
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
                    width: calc(60% - 8px);
                }

                .fct-card .profile_view .listActivity {
                    width: calc(40% - 8px);
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
                        <h5>
                            {{ $centro->nombre ?? 'Sense centre' }}
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
                        @if ($telefonoInstructor !== '' || $emailInstructor !== '' || $telefonoContacto !== '' || $emailContacto !== '')
                            <div class="fct-contact-links">
                                @if ($telefonoInstructor !== '')
                                    <a href="tel:{{ preg_replace('/\s+/', '', $telefonoInstructor) }}" class="btn btn-default btn-xs">
                                        <i class="fa fa-phone"></i> Instructor
                                    </a>
                                @endif
                                @if ($emailInstructor !== '')
                                    <a href="mailto:{{ $emailInstructor }}" class="btn btn-default btn-xs">
                                        <i class="fa fa-envelope"></i> Instructor
                                    </a>
                                @endif
                                @if ($telefonoContacto !== '' && $telefonoContacto !== $telefonoInstructor)
                                    <a href="tel:{{ preg_replace('/\s+/', '', $telefonoContacto) }}" class="btn btn-default btn-xs">
                                        <i class="fa fa-phone"></i> Centre
                                    </a>
                                @endif
                                @if ($emailContacto !== '' && $emailContacto !== $emailInstructor)
                                    <a href="mailto:{{ $emailContacto }}" class="btn btn-default btn-xs">
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
                        @isset (authUser()->emailItaca)
                            @forelse ($contactos as $contacto)
                                <x-activity :activity="$contacto" />
                                <br/>
                            @empty
                                <span class="text-muted">Sense evidències de seguiment.</span>
                            @endforelse
                        @endisset
                    </div>
                </div>
                <div class="col-xs-12 bottom text-center"
                     @if ($fct->asociacion === 3) style="background-color:orange"@endif
                >
                    <div class="col-xs-12 col-sm-5 emphasis">
                        <p class="ratings">
                            {{ strtoupper($centro->localidad ?? '') }}<br/>
                        </p>
                        <a href="{{ route('fct.show', ['id' => $fct->id]) }}" class="btn-success btn btn-xs" title="Mostrar Fct">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="{{ route('PanelColaboracion.colaboracion', ['id' => $fct->id, 'documento' => 'finEmpresa']) }}"
                           class="btn-success btn btn-xs"
                           title="Enviar Correu">
                            <i class="fa fa-envelope-o"></i>
                        </a>
                    </div>
                    <div class="col-xs-12 col-sm-7 emphasis">
                        <x-botones :panel="$panel" tipo="fct" :elemento="$fct"/>
                     </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
