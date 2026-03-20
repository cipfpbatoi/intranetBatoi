<div class="row">
    <div class="col-md-7 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>{{ $elemento->Empresa }}</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <p class="mb-3">
                    <span class="badge bg-info-subtle text-dark border">
                        {{ optional($elemento->Ciclo)->literal ?? 'Sense cicle' }}
                    </span>
                </p>

                <ul class="list-unstyled user_data">
                    <li>
                        <em class="fa fa-map-marker user-profile-icon"></em>
                        {{ $elemento->Centro->nombre }} / {{ $elemento->Centro->localidad }}
                    </li>
                    <li>
                        <em class="fa fa-user user-profile-icon"></em>
                        {{ $elemento->contacto ?: 'Sense contacte' }}
                    </li>
                    <li>
                        <em class="fa fa-phone user-profile-icon"></em>
                        {{ $elemento->telefono ?: 'Sense telèfon' }}
                    </li>
                    <li>
                        <em class="fa fa-envelope user-profile-icon"></em>
                        {{ $elemento->email ?: 'Sense email' }}
                    </li>
                    <li>
                        <em class="fa fa-clock-o user-profile-icon"></em>
                        {{ $elemento->Centro->horarios ?: 'Sense horari' }}
                    </li>
                    <li>
                        <em class="fa fa-user-secret user-profile-icon"></em>
                        {{ $elemento->profesor ?: 'Sense professor assignat' }}
                    </li>
                    <li>
                        <em class="fa fa-suitcase user-profile-icon"></em>
                        {{ $elemento->puestos }} lloc(s) de treball
                    </li>
                    <li>
                        <em class="fa fa-file-text-o user-profile-icon"></em>
                        {{ $elemento->Xestado ?: 'Sense estat' }}
                    </li>
                </ul>

                @if ($ultimContacte)
                    <hr/>
                    <h4>Últim seguiment</h4>
                    <p class="mb-1">
                        <strong>{{ $ultimContacte->document ?: $ultimContacte->action }}</strong>
                        · {{ $ultimContacte->created_at?->format('d/m/Y H:i') }}
                    </p>
                    @if (!empty($ultimContacte->comentari))
                        <p class="text-muted">{{ $ultimContacte->comentari }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-5 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Instructors</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                @forelse ($elemento->Centro->instructores->sortBy('surnames') as $instructor)
                    <div class="mb-3 pb-2" style="border-bottom: 1px solid #ececec;">
                        <div><strong>{{ $instructor->nombre }}</strong></div>
                        <div class="small text-muted">{{ $instructor->dni }}</div>
                        <div class="small">{{ $instructor->email ?: 'Sense email' }}</div>
                        <div class="small">{{ $instructor->telefono ?: 'Sense telèfon' }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Sense instructors associats al centre.</p>
                @endforelse
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h2>Empresa</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <p class="mb-2"><strong>{{ $elemento->Centro->Empresa->nombre }}</strong></p>
                <p class="text-muted">{{ $elemento->Centro->Empresa->cif ?: '' }}</p>
                <a href="{{ route('empresa.detalle', ['empresa' => $elemento->Centro->idEmpresa]) }}"
                   class="btn btn-primary btn-sm">
                    <em class="fa fa-building"></em> Anar a l'empresa
                </a>
            </div>
        </div>
    </div>
</div>
