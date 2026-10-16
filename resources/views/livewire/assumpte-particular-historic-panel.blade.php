<div>
    @if ($missatge !== '')
        <div class="alert alert-success" role="alert">{{ $missatge }}</div>
    @endif
    @if ($error !== '')
        <div class="alert alert-danger" role="alert">{{ $error }}</div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h2 class="h4 mb-1">Històric d’autoritzacions del curs</h2>
            <p class="text-muted mb-0">Consulta les autoritzacions i incorpora els dies ja gaudits.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('assumptes-particulars.direccion.index') }}">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
            Tornar a pendents
        </a>
    </div>

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

    <section class="card mb-3">
        <div class="card-header fw-semibold">Filtres</div>
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label for="filtre-professor" class="form-label mb-1">Professor/a</label>
                    <select id="filtre-professor" class="form-select" wire:model.live="filtreProfessor">
                        <option value="">Tot el professorat</option>
                        @foreach ($professors as $dni => $nom)
                            <option value="{{ $dni }}">{{ $nom }} ({{ $dni }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-2">
                    <label for="filtre-data-historic" class="form-label mb-1">Data de gaudi</label>
                    <input id="filtre-data-historic" type="date" class="form-control" wire:model.live="filtreData">
                </div>
                <div class="col-sm-6 col-md-2">
                    <label for="filtre-tipus" class="form-label mb-1">Tipus</label>
                    <select id="filtre-tipus" class="form-select" wire:model.live="filtreTipus">
                        <option value="">Tots</option>
                        <option value="lectiu">Lectiu</option>
                        <option value="no_lectiu">No lectiu</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-2">
                    <label for="filtre-origen" class="form-label mb-1">Origen</label>
                    <select id="filtre-origen" class="form-select" wire:model.live="filtreOrigen">
                        <option value="">Tots</option>
                        <option value="sollicitud">Sol·licitud</option>
                        <option value="regularitzacio">Regularització</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-2 text-md-end">
                    <button type="button" class="btn btn-outline-secondary" wire:click="netejarFiltres">
                        Netejar filtres
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        @if ($historial === [])
            <div class="card-body text-muted">No hi ha autoritzacions que coincidisquen amb els filtres.</div>
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
