<div>
    <p class="text-muted">
        Pilot funcional en convivència amb el panell legacy (<code>/direccion/expediente</code>).
    </p>

    @if ($error !== '')
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if ($message !== '')
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            @if ($hasReadyToPrint)
                <a class="btn btn-primary" href="/direccion/expediente/pdf" target="_blank" rel="noopener">
                    Imprimir expedients autoritzats ({{ $readyToPrintCount }})
                </a>
            @endif

            @if ($hasPendingAuthorization)
                <a class="btn btn-primary" href="/direccion/expediente/autorizar">
                    Autoritzar expedients pendents ({{ $pendingAuthorizationCount }})
                </a>
            @endif

            <a class="btn btn-default" href="/direccion/expediente">Tornar a versió legacy</a>
        </div>
    </div>

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-6">
            <label for="expediente-filter-text">Alumne, professor, tipus o mòdul</label>
            <input
                id="expediente-filter-text"
                type="text"
                class="form-control"
                placeholder="Escriu per filtrar"
                wire:model.live.debounce.300ms="filterText"
            >
        </div>
        <div class="col-md-3">
            <label for="expediente-filter-estat">Estat</label>
            <select
                id="expediente-filter-estat"
                class="form-control"
                wire:model.live="filterEstat"
            >
                <option value="">Tots</option>
                @foreach ($estatOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($rebutjarId !== null)
        <div class="panel panel-danger" style="margin-top: 20px;">
            <div class="panel-heading">Rebutjar expedient #{{ $rebutjarId }}</div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="motiu-rebutjar-expediente">Motiu</label>
                    <textarea
                        id="motiu-rebutjar-expediente"
                        class="form-control"
                        rows="3"
                        wire:model.defer="motiuRebutjar"
                    ></textarea>
                </div>
                <button type="button" class="btn btn-danger" wire:click="confirmarRebutjar">Confirmar rebuig</button>
                <button type="button" class="btn btn-default" wire:click="cancelarRebutjar">Cancelar</button>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Alumne</th>
                    <th>Professor</th>
                    <th>Data</th>
                    <th>Tipus</th>
                    <th>Mòdul</th>
                    <th>Estat</th>
                    <th>Operacions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expedientes as $expediente)
                    <tr wire:key="expediente-{{ $expediente['id'] }}">
                        <td>{{ $expediente['id'] }}</td>
                        <td>{{ $expediente['nomAlum'] }}</td>
                        <td>{{ $expediente['nomProfe'] }}</td>
                        <td>{{ $expediente['fecha'] }}</td>
                        <td>{{ $expediente['tipo'] }}</td>
                        <td>{{ $expediente['modulo'] !== '' ? $expediente['modulo'] : '-' }}</td>
                        <td>{{ $expediente['situacion'] }}</td>
                        <td style="white-space: nowrap;">
                            @if ((int) $expediente['estado'] === 1)
                                <button type="button" class="btn btn-success btn-xs" wire:click="acceptar({{ $expediente['id'] }})" title="Autoritzar">
                                    <i class="fa fa-check"></i>
                                </button>
                            @endif

                            @if ((int) $expediente['estado'] === 2)
                                <button type="button" class="btn btn-warning btn-xs" wire:click="desautoritzar({{ $expediente['id'] }})" title="Tornar a pendent">
                                    <i class="fa fa-undo"></i>
                                </button>
                            @endif

                            @if ((int) $expediente['estado'] === 1)
                                <button type="button" class="btn btn-danger btn-xs" wire:click="obrirRebutjar({{ $expediente['id'] }})" title="Rebutjar">
                                    <i class="fa fa-ban"></i>
                                </button>
                            @endif

                            <button type="button" class="btn btn-primary btn-xs" wire:click="mostrar({{ $expediente['id'] }})" title="Veure">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No hi ha expedients per als filtres actuals.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-center">
        {{ $paginator->links() }}
    </div>

    <div id="showExpediente" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Detall expedient</h4>
                </div>
                <div class="modal-body">
                    @if ($selectedExpediente !== null)
                        <ul class="list-unstyled">
                            <li><strong>Alumne:</strong> {{ $selectedExpediente['nomAlum'] }}</li>
                            <li><strong>Professor:</strong> {{ $selectedExpediente['nomProfe'] }}</li>
                            <li><strong>Tipus:</strong> {{ $selectedExpediente['tipo'] }}</li>
                            <li><strong>Mòdul:</strong> {{ $selectedExpediente['modulo'] !== '' ? $selectedExpediente['modulo'] : '-' }}</li>
                            <li><strong>Data:</strong> {{ $selectedExpediente['fecha'] }}</li>
                            <li><strong>Data tràmit:</strong> {{ $selectedExpediente['fechatramite'] !== '' ? $selectedExpediente['fechatramite'] : '-' }}</li>
                            <li><strong>Estat:</strong> {{ $selectedExpediente['situacion'] }}</li>
                            <li><strong>Explicació:</strong> {{ $selectedExpediente['explicacion'] !== '' ? $selectedExpediente['explicacion'] : '-' }}</li>
                        </ul>
                    @endif
                </div>
                <div class="modal-footer">
                    @if ($selectedExpediente !== null && $selectedExpediente['hasDocument'])
                        <a
                            class="btn btn-info"
                            href="{{ route('expediente.gestor', ['actividad' => $selectedExpediente['id']]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            Veure document
                        </a>
                    @endif

                    @if ($selectedExpediente !== null && $selectedExpediente['canPdf'])
                        <a
                            class="btn btn-default"
                            href="{{ route('expediente.pdf', ['expediente' => $selectedExpediente['id']]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            PDF
                        </a>
                    @endif

                    @if ($selectedExpediente !== null && $selectedExpediente['canShow'])
                        <a
                            class="btn btn-default"
                            href="{{ route('expediente.show', ['expediente' => $selectedExpediente['id']]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            Vista completa
                        </a>
                    @endif

                    <button type="button" class="btn btn-default" data-dismiss="modal">Tancar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function initExpedienteDireccionLivewireUi() {
            if (window.__expedienteLivewireUiInit) {
                return;
            }

            window.__expedienteLivewireUiInit = true;

            function showModalById(id) {
                var element = document.getElementById(id);
                if (!element || typeof window.jQuery === 'undefined') {
                    return;
                }

                window.jQuery(element).modal('show');
            }

            document.addEventListener('livewire:init', function () {
                Livewire.on('show-expediente-modal', function () {
                    showModalById('showExpediente');
                });
            });
        })();
    </script>
</div>
