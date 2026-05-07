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
        <div class="profile_details fct-card" >
            <div id="{{$fct->id}}" class="well profile_view">
                <div id="{{$fct->id}}" class="col-sm-12 fct">
                    <div class="left col-md-6 col-xs-12">
                        <h5>
                            {{ optional(optional($fct->Colaboracion)->Centro)->nombre ?? 'Sense centre' }}
                        </h5>
                        <ul class="list-unstyled">
                                @if ($fct->Instructor)
                                    <li>{{$fct->Instructor->nombre}}</li>
                                    <li>{{$fct->Instructor->telefono}}</li>
                                    <li>{{$fct->Instructor->email}}</li>
                                @else
                                    <li>No hi ha instructor. Cal corregir el problema</li>
                                @endif
                        </ul>
                        @foreach($panel->getBotones('profile') as $button)
                            {!! $button->show($fct) !!}
                        @endforeach
                    </div>
                    @if ($fct->asociacion === 3) <h5>-DUAL-</h5> @endif
                    <div class="col-md-6 listActivity">
                        @isset (authUser()->emailItaca)
                            @foreach ($fct->Contactos as $contacto)
                                <x-activity :activity="$contacto" />
                                <br/>
                            @endforeach
                        @endisset
                    </div>
                </div>
                <div class="col-xs-12 bottom text-center"
                     @if ($fct->asociacion === 3) style="background-color:orange"@endif
                >
                    <div class="col-xs-12 col-sm-5 emphasis">
                        <p class="ratings">
                            {{ strtoupper(optional(optional($fct->Colaboracion)->Centro)->localidad ?? '') }}<br/>
                        </p>
                        <em class="btn-success btn btn-xs">{{count($fct->Alumnos)}}</em>
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
                        <x-botones :panel="$panel" tipo="fct" :elemento="$elemento ?? null"/>
                     </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
