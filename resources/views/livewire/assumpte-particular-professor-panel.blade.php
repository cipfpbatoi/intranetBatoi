<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-1">Assumptes particulars</h2>
            <p class="text-muted mb-0">Curs {{ $curs }}</p>
        </div>
    </div>

    @if ($missatge !== '')
        <div class="alert alert-success" role="alert">{{ $missatge }}</div>
    @endif
    @if ($error !== '')
        <div class="alert alert-danger" role="alert">{{ $error }}</div>
    @endif

    <div class="row g-3 mb-4">
        @foreach (['lectiu' => 'Dies lectius', 'no_lectiu' => 'Dies no lectius'] as $tipus => $titol)
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="h6 text-muted">{{ $titol }}</h3>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="display-6">{{ number_format($saldos[$tipus]['disponible'] ?? 0, 2, ',', '.') }}</span>
                            <span>disponibles</span>
                        </div>
                        <small class="text-muted">
                            {{ $saldos[$tipus]['pendent'] ?? 0 }} peticions pendents reservades
                        </small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Nova sol·licitud</div>
        <div class="card-body">
            <form wire:submit="previsualitzar">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="data-gaudi" class="form-label">Data de gaudi</label>
                        <input
                            id="data-gaudi"
                            type="date"
                            class="form-control @error('dataGaudi') is-invalid @enderror"
                            wire:model="dataGaudi"
                            min="{{ now()->addDay()->toDateString() }}"
                            max="{{ now()->addMonthNoOverflow()->toDateString() }}"
                        >
                        @error('dataGaudi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label for="motivacio" class="form-label">Motivació excepcional</label>
                        <textarea
                            id="motivacio"
                            class="form-control @error('motivacioExcepcional') is-invalid @enderror"
                            rows="2"
                            wire:model="motivacioExcepcional"
                            placeholder="Obligatòria si falten menys de 7 dies naturals"
                        ></textarea>
                        @error('motivacioExcepcional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="pla-activitats" class="form-label">Pla d’activitats</label>
                        <textarea
                            id="pla-activitats"
                            class="form-control @error('plaActivitats') is-invalid @enderror"
                            rows="5"
                            wire:model="plaActivitats"
                            placeholder="Grups afectats, tasques previstes i indicacions per a l’alumnat"
                        ></textarea>
                        <div class="form-text">És obligatori quan el dia seleccionat és lectiu.</div>
                        @error('plaActivitats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3" wire:loading.attr="disabled">
                    Comprovar sol·licitud
                </button>
            </form>

            @if ($previsualitzacio !== null)
                <div class="alert alert-info mt-4 mb-0">
                    <h3 class="h6">Comprovació correcta</h3>
                    <dl class="row mb-3">
                        <dt class="col-sm-3">Data</dt>
                        <dd class="col-sm-9">{{ \Carbon\Carbon::parse($previsualitzacio['data'])->format('d/m/Y') }}</dd>
                        <dt class="col-sm-3">Tipus</dt>
                        <dd class="col-sm-9">{{ __('assumptes_particulars.tipus.' . $previsualitzacio['tipus']) }}</dd>
                        <dt class="col-sm-3">Torn</dt>
                        <dd class="col-sm-9">{{ __('assumptes_particulars.torns.' . $previsualitzacio['torn']) }}</dd>
                        <dt class="col-sm-3">Saldo disponible</dt>
                        <dd class="col-sm-9">{{ number_format($previsualitzacio['saldo'], 2, ',', '.') }} dies</dd>
                    </dl>
                    @if ($previsualitzacio['excepcional'])
                        <p class="mb-3"><strong>Petició excepcional:</strong> quedarà subjecta a valoració de Direcció.</p>
                    @endif
                    <button
                        type="button"
                        class="btn btn-success"
                        wire:click="enviar"
                        wire:loading.attr="disabled"
                    >
                        Confirmar i enviar
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-semibold">Les meues sol·licituds</div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipus</th>
                        <th>Torn</th>
                        <th>Estat</th>
                        <th>Resolució</th>
                        <th class="text-end">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peticions as $peticio)
                        <tr wire:key="assumpte-particular-{{ $peticio->id }}">
                            <td>{{ $peticio->data_gaudi->format('d/m/Y') }}</td>
                            <td>{{ __('assumptes_particulars.tipus.' . $peticio->tipus) }}</td>
                            <td>{{ __('assumptes_particulars.torns.' . $peticio->torn) }}</td>
                            <td>{{ __('assumptes_particulars.estats.' . $peticio->estat) }}</td>
                            <td>{{ $peticio->resolucio ?: '—' }}</td>
                            <td class="text-end text-nowrap">
                                @if ($peticio->falta_id)
                                    <a
                                        class="btn btn-sm btn-outline-primary"
                                        href="{{ route('falta.document', ['falta' => $peticio->falta_id]) }}"
                                    >
                                        Descarregar resolució
                                    </a>
                                @endif
                                @if ($peticio->estaPendent())
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="cancelLarPeticio({{ $peticio->id }})"
                                        wire:confirm="Segur que vols cancel·lar esta sol·licitud?"
                                    >
                                        Cancel·lar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Encara no has presentat cap sol·licitud.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($peticions->hasPages())
            <div class="card-footer">
                {{ $peticions->links() }}
            </div>
        @endif
    </div>
</div>
