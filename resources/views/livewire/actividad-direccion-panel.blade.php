<div>
    @if ($error !== '')
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if ($message !== '')
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            @if ($hasAuthorizedToPrint)
                <a
                    class="btn btn-primary js-bulk-reload"
                    href="/direccion/actividad/pdf"
                    target="_blank"
                    rel="noopener"
                    data-bulk-action="print"
                >
                    Imprimir activitats autoritzades ({{ $authorizedToPrintCount }})
                </a>
            @endif

            @if ($hasPendingAuthorization)
                <a class="btn btn-primary js-bulk-reload" href="/direccion/actividad/autorizar" data-bulk-action="authorize">
                    Autoritzar activitats pendents ({{ $pendingAuthorizationCount }})
                </a>
            @endif

        </div>
    </div>

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-5">
            <label for="actividad-filter-professor">Professor o activitat</label>
            <input
                id="actividad-filter-professor"
                type="text"
                class="form-control"
                list="actividad-professor-options"
                placeholder="Professor o activitat"
                wire:model.live.debounce.300ms="filterProfessor"
            >
            <datalist id="actividad-professor-options">
                @foreach ($professorOptions as $option)
                    <option value="{{ $option }}"></option>
                @endforeach
            </datalist>
        </div>
        <div class="col-md-3">
            <label for="actividad-filter-departament">Departament</label>
            <select
                id="actividad-filter-departament"
                class="form-control"
                wire:model.live="filterDepartament"
            >
                <option value="">Tots</option>
                @foreach ($departamentOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="actividad-filter-estat">Estat</label>
            <select
                id="actividad-filter-estat"
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

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Coordinador</th>
                    <th>Activitat</th>
                    <th>Des de</th>
                    <th>Estat</th>
                    <th>Operacions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($actividades as $actividad)
                    <tr wire:key="actividad-{{ $actividad['id'] }}">
                        <td>{{ $actividad['id'] }}</td>
                        <td>{{ $actividad['coordinador'] }}</td>
                        <td>{{ $actividad['name'] }}</td>
                        <td>{{ $actividad['desde'] }}</td>
                        <td>{{ $actividad['situacion'] }}</td>
                        <td style="white-space: nowrap;">
                            @if ((int) $actividad['estado'] === 1)
                                <button type="button" class="btn btn-success btn-xs" wire:click="acceptar({{ $actividad['id'] }})" title="Autoritzar">
                                    <i class="fa fa-check"></i>
                                </button>
                            @endif

                            @if ($actividad['canDesautorize'])
                                <button type="button" class="btn btn-warning btn-xs" wire:click="desautoritzar({{ $actividad['id'] }})" title="Tornar a l'estat anterior">
                                    <i class="fa fa-undo"></i>
                                </button>
                            @endif

                            @if ((int) $actividad['estado'] > 0 && (int) $actividad['estado'] < 4)
                                <button type="button" class="btn btn-danger btn-xs" wire:click="obrirRebutjar({{ $actividad['id'] }})" title="Rebutjar">
                                    <i class="fa fa-ban"></i>
                                </button>
                            @endif

                            @if ($actividad['canMarkItaca'])
                                <button type="button" class="btn btn-info btn-xs" wire:click="marcarItaca({{ $actividad['id'] }})" title="Marcar ITACA">
                                    <i class="fa fa-bullseye"></i>
                                </button>
                            @endif

                            <button type="button" class="btn btn-primary btn-xs" wire:click="mostrar({{ $actividad['id'] }})" title="Veure">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hi ha activitats per als filtres actuals.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-center">
        {{ $paginator->links() }}
    </div>

    @if ($rebutjarId !== null)
        <div class="panel panel-danger" style="margin-top: 20px;">
            <div class="panel-heading">Rebutjar activitat #{{ $rebutjarId }}</div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="motiu-rebutjar-actividad">Motiu</label>
                    <textarea
                        id="motiu-rebutjar-actividad"
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

    <div id="showActividad" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Detall activitat</h4>
                </div>
                <div class="modal-body">
                    @if ($selectedActividad !== null)
                        <ul class="list-unstyled">
                            <li><strong>Activitat:</strong> {{ $selectedActividad['name'] }}</li>
                            <li><strong>Descripció:</strong> {{ $selectedActividad['descripcion'] !== '' ? $selectedActividad['descripcion'] : '-' }}</li>
                            <li><strong>Tipus activitat:</strong> {{ $selectedActividad['tipoActividad'] }}</li>
                            <li><strong>Departament:</strong> {{ $selectedActividad['departamento'] }}</li>
                            <li><strong>Coordinador:</strong> {{ $selectedActividad['coordinador'] }}</li>
                            <li><strong>Professorat:</strong> {{ implode(', ', $selectedActividad['profesores']) !== '' ? implode(', ', $selectedActividad['profesores']) : '-' }}</li>
                            <li><strong>Des de:</strong> {{ $selectedActividad['desde'] }}</li>
                            <li><strong>Fins:</strong> {{ $selectedActividad['hasta'] }}</li>
                            <li><strong>Tipus:</strong> {{ $selectedActividad['tipo'] }}</li>
                            <li><strong>Estat:</strong> {{ $selectedActividad['situacion'] }}</li>
                        </ul>
                    @endif
                </div>
                <div class="modal-footer">
                    @if ($selectedActividad !== null && $selectedActividad['hasDocument'])
                        <a
                            class="btn btn-info"
                            href="{{ route('actividad.direccion.gestor', ['actividad' => $selectedActividad['id']]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            Veure document
                        </a>
                    @endif

                    @if ($selectedActividad !== null && $selectedActividad['canPdfValue'])
                        <a
                            class="btn btn-default"
                            href="{{ route('actividad.direccion.pdfVal', ['actividad' => $selectedActividad['id']]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            PDF valoració
                        </a>
                    @endif

                    <button type="button" class="btn btn-default" data-dismiss="modal">Tancar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function initActividadDireccionLivewireUi() {
            if (window.__actividadLivewireUiInit) {
                return;
            }

            window.__actividadLivewireUiInit = true;

            function showModalById(id) {
                var element = document.getElementById(id);
                if (!element || typeof window.jQuery === 'undefined') {
                    return;
                }

                window.jQuery(element).modal('show');
            }

            document.addEventListener('livewire:init', function () {
                Livewire.on('show-actividad-modal', function () {
                    showModalById('showActividad');
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
