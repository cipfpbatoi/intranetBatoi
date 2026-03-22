<div>
    @if ($error !== '')
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if ($message !== '')
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            @if ($hasReadyToPrint)
                <a
                    class="btn btn-primary js-bulk-reload"
                    href="/direccion/expediente/pdf"
                    target="_blank"
                    rel="noopener"
                    data-bulk-action="print"
                >
                    Imprimir expedients autoritzats ({{ $readyToPrintCount }})
                </a>
            @endif

            @if ($hasPendingAuthorization)
                <a class="btn btn-primary js-bulk-reload" href="/direccion/expediente/autorizar" data-bulk-action="authorize">
                    Autoritzar expedients pendents ({{ $pendingAuthorizationCount }})
                </a>
            @endif
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
        <div class="card border-danger" style="margin-top: 20px;">
            <div class="card-header">Rebutjar expedient #{{ $rebutjarId }}</div>
            <div class="card-body">
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
                <button type="button" class="btn btn-secondary" wire:click="cancelarRebutjar">Cancelar</button>
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

    <div id="showExpediente" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title">Detall expedient</h5>
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
                            href="{{ route('expediente.direccion.gestor', ['expediente' => $selectedExpediente['id']]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            Veure document
                        </a>
                    @endif

                    @if ($selectedExpediente !== null && $selectedExpediente['canPdf'])
                        <a
                            class="btn btn-secondary"
                            href="{{ route('expediente.direccion.pdf.item', ['expediente' => $selectedExpediente['id']]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            PDF
                        </a>
                    @endif

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tancar</button>
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
                if (window.intranetUiHelpers && typeof window.intranetUiHelpers.showModal === 'function') {
                    window.intranetUiHelpers.showModal(id);
                    return;
                }

                var element = document.getElementById(id);
                if (!element || !window.bootstrap || !window.bootstrap.Modal) {
                    return;
                }

                window.bootstrap.Modal.getOrCreateInstance(element).show();
            }

            document.addEventListener('livewire:init', function () {
                Livewire.on('show-expediente-modal', function () {
                    showModalById('showExpediente');
                });
            });

            document.addEventListener('click', function (event) {
                var button = event.target.closest('.js-bulk-reload');
                if (!button) {
                    return;
                }

                var action = button.dataset.bulkAction || '';
                var delay = action === 'print' ? 1200 : 500;

                window.setTimeout(function () {
                    window.location.reload();
                }, delay);
            });
        })();
    </script>
</div>
