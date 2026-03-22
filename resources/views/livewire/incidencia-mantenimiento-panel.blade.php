<div>
    @if ($error !== '')
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if ($message !== '')
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-8">
            <label for="incidencia-filter-text">Descripció, tipus, creador o responsable</label>
            <input
                id="incidencia-filter-text"
                type="text"
                class="form-control"
                placeholder="Escriu per filtrar"
                wire:model.live.debounce.300ms="filterText"
            >
        </div>
        <div class="col-md-3">
            <label for="incidencia-filter-estat">Estat</label>
            <select
                id="incidencia-filter-estat"
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
        <div class="card border-danger" style="margin-bottom: 20px;">
            <div class="card-header">Rebutjar incidència #{{ $rebutjarId }}</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="motiu-rebutjar-incidencia">Motiu</label>
                    <textarea
                        id="motiu-rebutjar-incidencia"
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

    @if ($resoldreId !== null)
        <div class="card border-success" style="margin-bottom: 20px;">
            <div class="card-header">Resoldre incidència #{{ $resoldreId }}</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="motiu-resoldre-incidencia">Solució o comentari</label>
                    <textarea
                        id="motiu-resoldre-incidencia"
                        class="form-control"
                        rows="3"
                        wire:model.defer="motiuResoldre"
                    ></textarea>
                </div>
                <button type="button" class="btn btn-success" wire:click="confirmarResoldre">Confirmar resolució</button>
                <button type="button" class="btn btn-secondary" wire:click="cancelarResoldre">Cancelar</button>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Tipus</th>
                    <th>Descripció</th>
                    <th>Responsable</th>
                    <th>Estat</th>
                    <th>Ordre</th>
                    <th>Operacions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($incidencies as $incidencia)
                    <tr wire:key="incidencia-{{ $incidencia['id'] }}">
                        <td>{{ $incidencia['id'] }}</td>
                        <td>{{ $incidencia['fecha'] }}</td>
                        <td>{{ $incidencia['tipo'] }}</td>
                        <td>{{ $incidencia['descripcion'] }}</td>
                        <td>{{ $incidencia['responsable'] }}</td>
                        <td>{{ $incidencia['situacion'] }}</td>
                        <td>
                            @if ($incidencia['orden'] !== null)
                                <a href="{{ route('orden.anexo', ['orden' => $incidencia['orden']]) }}" class="btn btn-primary btn-xs">
                                    Ordre {{ $incidencia['orden'] }}
                                </a>
                            @else
                                <span class="text-muted">Sense ordre</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            @if ($incidencia['canAuthorize'])
                                <button type="button" class="btn btn-success btn-xs" wire:click="acceptar({{ $incidencia['id'] }})" title="Autoritzar">
                                    <i class="fa fa-check"></i>
                                </button>
                            @endif

                            @if ($incidencia['canUnauthorize'])
                                <button type="button" class="btn btn-warning btn-xs" wire:click="desautoritzar({{ $incidencia['id'] }})" title="Tornar a l'estat anterior">
                                    <i class="fa fa-undo"></i>
                                </button>
                            @endif

                            @if ($incidencia['canRefuse'])
                                <button type="button" class="btn btn-danger btn-xs" wire:click="obrirRebutjar({{ $incidencia['id'] }})" title="Rebutjar">
                                    <i class="fa fa-ban"></i>
                                </button>
                            @endif

                            @if ($incidencia['canResolve'])
                                <button type="button" class="btn btn-success btn-xs" wire:click="obrirResoldre({{ $incidencia['id'] }})" title="Resoldre">
                                    <i class="fa fa-wrench"></i>
                                </button>
                            @endif

                            @if ($incidencia['canAssignOrder'])
                                <button type="button" class="btn btn-info btn-xs" wire:click="assignarOrden({{ $incidencia['id'] }})" title="Assignar ordre">
                                    <i class="fa fa-link"></i>
                                </button>
                            @endif

                            @if ($incidencia['canRemoveOrder'])
                                <button type="button" class="btn btn-default btn-xs" wire:click="llevarOrden({{ $incidencia['id'] }})" title="Llevar ordre">
                                    <i class="fa fa-unlink"></i>
                                </button>
                            @endif

                            <button type="button" class="btn btn-primary btn-xs" wire:click="mostrar({{ $incidencia['id'] }})" title="Veure">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No hi ha incidències per als filtres actuals.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-center">
        {{ $paginator->links() }}
    </div>

    <div id="showIncidencia" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title">Detall incidència</h5>
                </div>
                <div class="modal-body">
                    @if ($selectedIncidencia !== null)
                        <ul class="list-unstyled">
                            <li><strong>ID:</strong> {{ $selectedIncidencia['id'] }}</li>
                            <li><strong>Tipus:</strong> {{ $selectedIncidencia['tipo'] }}</li>
                            <li><strong>Subtipus:</strong> {{ $selectedIncidencia['subtipo'] !== '' ? $selectedIncidencia['subtipo'] : '-' }}</li>
                            <li><strong>Data:</strong> {{ $selectedIncidencia['fecha'] }}</li>
                            <li><strong>Estat:</strong> {{ $selectedIncidencia['situacion'] }}</li>
                            <li><strong>Creador:</strong> {{ $selectedIncidencia['creador'] !== '' ? $selectedIncidencia['creador'] : '-' }}</li>
                            <li><strong>Responsable:</strong> {{ $selectedIncidencia['responsable'] }}</li>
                            <li><strong>Espai:</strong> {{ $selectedIncidencia['espacio'] !== '' ? $selectedIncidencia['espacio'] : '-' }}</li>
                            <li><strong>Material:</strong> {{ $selectedIncidencia['material'] !== '' ? $selectedIncidencia['material'] : '-' }}</li>
                            <li><strong>Descripció:</strong> {{ $selectedIncidencia['descripcion'] }}</li>
                            <li><strong>Observacions:</strong> {{ $selectedIncidencia['observaciones'] !== '' ? $selectedIncidencia['observaciones'] : '-' }}</li>
                            <li><strong>Solució:</strong> {{ $selectedIncidencia['solucion'] !== '' ? $selectedIncidencia['solucion'] : '-' }}</li>
                            <li><strong>Data solució:</strong> {{ $selectedIncidencia['fechasolucion'] !== '' ? $selectedIncidencia['fechasolucion'] : '-' }}</li>
                        </ul>

                        @if ($selectedIncidencia['imagen'] !== '')
                            <hr>
                            <img
                                src="{{ $selectedIncidencia['imagen'] }}"
                                alt="Imatge incidència"
                                style="max-width: 100%; height: auto; border-radius: 4px;"
                            >
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tancar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function initIncidenciaMantenimientoLivewireUi() {
            if (window.__incidenciaMantenimientoLivewireUiInit) {
                return;
            }

            window.__incidenciaMantenimientoLivewireUiInit = true;

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
                Livewire.on('show-incidencia-modal', function () {
                    showModalById('showIncidencia');
                });
            });
        })();
    </script>
</div>
