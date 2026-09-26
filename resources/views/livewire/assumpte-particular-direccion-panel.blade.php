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
            Pots consultar i denegar peticions. Cal tindre el rol de Direcció per a autoritzar-les amb la rúbrica de la directora configurada.
        </div>
    @else
        @unless ($teRubricaDirectora)
            <div class="alert alert-warning" role="alert">
                Per a autoritzar peticions, la directora configurada ha de tindre la rúbrica guardada en el seu perfil.
            </div>
        @endunless
    @endunless

    @if ($potRegularitzar)
        <section class="card mb-4">
            <div class="card-header fw-semibold">Incorporar un dia ja gaudit</div>
            <div class="card-body">
                <p class="text-muted small">
                    Esta operació incorpora una autorització anterior a l’històric i consumix saldo,
                    però no crea cap falta ni resolució PDF.
                </p>
                <form wire:submit="regularitzar">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-5">
                            <label for="professor-regularitzacio" class="form-label">Professor/a</label>
                            <select
                                id="professor-regularitzacio"
                                class="form-select @error('professorRegularitzacio') is-invalid @enderror"
                                wire:model="professorRegularitzacio"
                            >
                                <option value="">Selecciona el professorat</option>
                                @foreach ($professors as $dni => $nom)
                                    <option value="{{ $dni }}">{{ $nom }} ({{ $dni }})</option>
                                @endforeach
                            </select>
                            @error('professorRegularitzacio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <label for="data-regularitzacio" class="form-label">Data ja gaudida</label>
                            <input
                                id="data-regularitzacio"
                                type="date"
                                max="{{ now()->toDateString() }}"
                                class="form-control @error('dataRegularitzacio') is-invalid @enderror"
                                wire:model="dataRegularitzacio"
                            >
                            @error('dataRegularitzacio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <label for="tipus-regularitzacio" class="form-label">Tipus</label>
                            <select
                                id="tipus-regularitzacio"
                                class="form-select @error('tipusRegularitzacio') is-invalid @enderror"
                                wire:model="tipusRegularitzacio"
                            >
                                <option value="lectiu">Lectiu</option>
                                <option value="no_lectiu">No lectiu</option>
                            </select>
                            @error('tipusRegularitzacio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-2 text-lg-end">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                wire:confirm="Segur que vols incorporar este dia a l’històric i consumir-lo del saldo?"
                            >
                                Incorporar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    @endif

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
                                        @if ($potAutoritzar && $teRubricaDirectora && $grup['data'] <= now()->addDays(7)->toDateString())
                                            wire:click="autoritzar({{ $peticio['id'] }})"
                                            wire:confirm="Segur que vols autoritzar esta petició i generar el document firmat?"
                                        @else
                                            disabled
                                            title="{{ !$potAutoritzar ? 'Cal el rol de Direcció per a autoritzar' : (!$teRubricaDirectora ? 'Cal la rúbrica de la directora configurada' : 'Només es poden autoritzar els pròxims set dies naturals') }}"
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

    <section class="card mt-4">
        <div class="card-header">
            <h2 class="h5 mb-0">Històric d’autoritzacions del curs</h2>
        </div>
        @if ($historial === [])
            <div class="card-body text-muted">Encara no hi ha autoritzacions en este curs.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Data de gaudi</th>
                            <th>Professor/a</th>
                            <th>Tipus</th>
                            <th>Origen</th>
                            <th>Tramitada per</th>
                            <th>Data de tramitació</th>
                            <th class="text-end">Resolució</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historial as $peticio)
                            <tr wire:key="historic-assumpte-{{ $peticio['id'] }}">
                                <td>{{ $peticio['data_formatada'] }}</td>
                                <td>
                                    <strong>{{ $peticio['professor'] }}</strong>
                                    <div class="small text-muted">{{ $peticio['dni'] }}</div>
                                </td>
                                <td>{{ __('assumptes_particulars.tipus.' . $peticio['tipus']) }}</td>
                                <td>
                                    @if ($peticio['origen'] === \Intranet\Entities\AssumpteParticular::ORIGEN_REGULARITZACIO)
                                        <span class="badge bg-info text-dark">Regularització</span>
                                    @else
                                        <span class="badge bg-secondary">Sol·licitud</span>
                                    @endif
                                </td>
                                <td>{{ $peticio['resolta_per'] }}</td>
                                <td>{{ $peticio['resolta_at'] }}</td>
                                <td class="text-end">
                                    @if ($peticio['te_document'])
                                        <a
                                            class="btn btn-sm btn-outline-primary"
                                            href="{{ route('assumptes-particulars.document', ['assumpteParticular' => $peticio['id']]) }}"
                                        >
                                            Descarregar
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
