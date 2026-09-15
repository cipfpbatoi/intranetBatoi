<div>
    @if ($missatge !== '')
        <div class="alert alert-success" role="alert">{{ $missatge }}</div>
    @endif
    @if ($error !== '')
        <div class="alert alert-danger" role="alert">{{ $error }}</div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-1">Assumptes particulars pendents</h2>
            <p class="text-muted mb-0">Peticions agrupades per data i ordenades per prioritat.</p>
        </div>
        <button type="button" class="btn btn-outline-primary" wire:click="recarregar" wire:loading.attr="disabled">
            <i class="fa fa-refresh" aria-hidden="true"></i>
            Actualitzar
        </button>
    </div>

    @unless ($potAutoritzar)
        <div class="alert alert-info" role="status">
            Pots consultar i denegar peticions. Només la directora configurada pot autoritzar-les amb la seua rúbrica.
        </div>
    @else
        @unless ($teRubricaDirectora)
            <div class="alert alert-warning" role="alert">
                Per a autoritzar peticions has de
                <a href="{{ route('files.edit') }}" class="alert-link">pujar la rúbrica des de Fitxers del perfil</a>.
            </div>
        @endunless
    @endunless

    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-sm-6 col-md-4">
                    <label for="filtre-data" class="form-label mb-1"><strong>Filtrar per data</strong></label>
                    <input
                        id="filtre-data"
                        type="date"
                        class="form-control @error('filtreData') is-invalid @enderror"
                        wire:model.live="filtreData"
                    >
                    @error('filtreData')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @if ($filtreData !== '')
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-secondary" wire:click="netejarFiltreData">
                            Mostrar totes les dates
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($grups === [])
        <div class="alert alert-info" role="status">
            @if ($filtreData !== '')
                No hi ha peticions pendents per a la data seleccionada.
            @else
                No hi ha peticions pendents d’assumptes particulars.
            @endif
        </div>
    @endif

    @foreach ($grups as $grup)
        <section class="card mb-4" wire:key="assumptes-dia-{{ $grup['data'] }}">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h3 class="h5 mb-0">{{ $grup['data_formatada'] }}</h3>
                <div>
                    <span class="badge bg-secondary">Quota: {{ $grup['quota_total'] }}</span>
                    <span class="badge bg-primary">Autoritzades: {{ $grup['autoritzades_total'] }}</span>
                    <span class="badge bg-success">Disponibles: {{ $grup['disponibles_total'] }}</span>
                </div>
            </div>

            <div class="card-body border-bottom py-2">
                <div class="row g-2">
                    @foreach ($grup['quotes'] as $torn => $quota)
                        <div class="col-sm-6 col-lg-3">
                            <div class="border rounded p-2 h-100">
                                <strong>{{ __('assumptes_particulars.torns.' . $torn) }}</strong>
                                <div class="small text-muted">
                                    {{ $quota['autoritzades'] }} autoritzades de {{ $quota['quota'] }} ·
                                    {{ $quota['disponibles'] }} disponibles
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Prioritat</th>
                            <th>Professor</th>
                            <th>Tipus</th>
                            <th>Torn</th>
                            <th class="text-center">Dies gaudits</th>
                            <th class="text-center">Hores afectades</th>
                            <th>Motivació excepcional</th>
                            <th>Estat i avisos</th>
                            <th class="text-end">Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grup['peticions'] as $index => $peticio)
                            <tr wire:key="assumpte-direccio-{{ $peticio['id'] }}">
                                <td>
                                    <span class="badge bg-dark">{{ $index + 1 }}</span>
                                    <div class="small text-muted">{{ $peticio['sollicitada_at'] }}</div>
                                </td>
                                <td>
                                    <strong>{{ $peticio['professor'] }}</strong>
                                    <div class="small text-muted">{{ $peticio['dni'] }}</div>
                                </td>
                                <td>{{ __('assumptes_particulars.tipus.' . $peticio['tipus']) }}</td>
                                <td>
                                    {{ __('assumptes_particulars.torns.' . $peticio['torn']) }}
                                    @if ($peticio['torn_actual'] !== $peticio['torn'])
                                        <div class="small text-warning">
                                            Ara: {{ __('assumptes_particulars.torns.' . $peticio['torn_actual']) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">{{ $peticio['dies_gaudits'] }}</td>
                                <td class="text-center">{{ $peticio['hores_lectives'] }}</td>
                                <td>
                                    @if ($peticio['excepcional'])
                                        <span class="badge bg-warning text-dark mb-1">Excepcional</span>
                                        <div>{{ $peticio['motivacio_excepcional'] }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ __('assumptes_particulars.estats.' . $peticio['estat']) }}
                                    </span>
                                    @foreach ($peticio['avisos'] as $avis)
                                        <div class="alert alert-warning small py-1 px-2 mt-1 mb-0" role="alert">
                                            {{ $avis }}
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-end text-nowrap">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success"
                                        @if ($potAutoritzar && $teRubricaDirectora)
                                            wire:click="autoritzar({{ $peticio['id'] }})"
                                            wire:confirm="Segur que vols autoritzar esta petició i generar el document firmat?"
                                        @else
                                            disabled
                                            title="{{ $potAutoritzar ? 'Cal pujar la rúbrica al perfil' : 'Només la directora configurada pot autoritzar' }}"
                                        @endif
                                    >
                                        Autoritzar
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="seleccionarDenegacio({{ $peticio['id'] }})"
                                    >
                                        Denegar
                                    </button>
                                </td>
                            </tr>
                            @if ($peticioADenegar === $peticio['id'])
                                <tr wire:key="denegacio-assumpte-{{ $peticio['id'] }}">
                                    <td colspan="9">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-9">
                                                <label for="motiu-denegacio-{{ $peticio['id'] }}" class="form-label">
                                                    Motiu de la denegació
                                                </label>
                                                <textarea
                                                    id="motiu-denegacio-{{ $peticio['id'] }}"
                                                    class="form-control @error('motiuDenegacio') is-invalid @enderror"
                                                    rows="2"
                                                    maxlength="2000"
                                                    wire:model="motiuDenegacio"
                                                ></textarea>
                                                @error('motiuDenegacio')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3 text-md-end">
                                                <button type="button" class="btn btn-danger" wire:click="denegar">
                                                    Confirmar denegació
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-secondary"
                                                    wire:click="cancelLarDenegacio"
                                                >
                                                    Cancel·lar
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
</div>
