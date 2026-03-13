<div>
    <h2>Comissions - Pilot Livewire Direcció</h2>

    <p class="text-muted">
        Pilot funcional en convivència amb el panell legacy (<code>/direccion/comision</code>).
    </p>

    @if ($error !== '')
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if ($message !== '')
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <div class="mb-3">
        <a class="btn btn-default" href="/direccion/comision">Tornar a versió legacy</a>
    </div>

    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-4">
            <label for="filterProfessor"><strong>Filtrar per professor</strong></label>
            <input
                id="filterProfessor"
                type="text"
                class="form-control"
                list="professorSuggestions"
                placeholder="Escriu nom, cognoms o DNI"
                wire:model.live.debounce.300ms="filterProfessor"
            >
            <datalist id="professorSuggestions">
                @foreach ($professorOptions as $professorLabel)
                    <option value="{{ $professorLabel }}"></option>
                @endforeach
            </datalist>
        </div>
        <div class="col-md-4">
            <label for="filterEstat"><strong>Filtrar per estat</strong></label>
            <select id="filterEstat" class="form-control" wire:model.live="filterEstat">
                <option value="">Tots</option>
                @foreach ($estatOptions as $estat => $label)
                    <option value="{{ $estat }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($rebutjarId !== null)
        <div class="panel panel-default">
            <div class="panel-heading"><strong>Rebutjar comissió #{{ $rebutjarId }}</strong></div>
            <div class="panel-body">
                <label for="motiuRebutjar">Motiu</label>
                <textarea id="motiuRebutjar" class="form-control" wire:model.defer="motiuRebutjar"></textarea>
                <div class="mt-2" style="margin-top: 10px;">
                    <button class="btn btn-danger" type="button" wire:click="confirmarRebutjar">Confirmar rebuig</button>
                    <button class="btn btn-default" type="button" wire:click="cancelarRebutjar">Cancel·lar</button>
                </div>
            </div>
        </div>
    @endif

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Professor</th>
            <th>Servei</th>
            <th>Des de</th>
            <th>Total</th>
            <th>Estat</th>
            <th>Accions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($comisiones as $comision)
            <tr wire:key="comision-{{ $comision['id'] }}">
                <td>{{ $comision['id'] }}</td>
                <td>{{ $comision['professor'] }}</td>
                <td>{{ $comision['servicio'] }}</td>
                <td>{{ $comision['desde'] }}</td>
                <td>{{ number_format($comision['total'], 2, ',', '.') }} €</td>
                <td>{{ $comision['situacion'] }}</td>
                <td>
                    @if ((int) $comision['estado'] === 1)
                        <button type="button" class="btn btn-success btn-xs" wire:click="acceptar({{ $comision['id'] }})" title="Autoritzar">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                    @endif

                    @if ((int) $comision['estado'] === 2)
                        <button type="button" class="btn btn-warning btn-xs" wire:click="desautoritzar({{ $comision['id'] }})" title="Tornar a pendent">
                            <i class="fa fa-undo" aria-hidden="true"></i>
                        </button>
                    @endif

                    @if ((int) $comision['estado'] === 1)
                        <button type="button" class="btn btn-danger btn-xs" wire:click="obrirRebutjar({{ $comision['id'] }})" title="Rebutjar">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    @endif

                    <button
                        type="button"
                        class="btn btn-info btn-xs"
                        wire:click="mostrar({{ $comision['id'] }})"
                        title="Veure"
                    >
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No hi ha comissions per mostrar.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div id="showComision" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                    <h4 class="modal-title">Comissió de servei</h4>
                </div>
                <div class="modal-body">
                    @if ($selectedComision !== null)
                        <ul class="list-unstyled">
                            <li><strong>Professor:</strong> {{ $selectedComision['professor'] }}</li>
                            <li><strong>Servei:</strong> {{ $selectedComision['servicio'] }}</li>
                            <li><strong>Des de:</strong> {{ $selectedComision['desde'] }}</li>
                            <li><strong>Fins:</strong> {{ $selectedComision['hasta'] }}</li>
                            <li><strong>Estat:</strong> {{ $selectedComision['situacion'] }}</li>
                            <li><strong>Total:</strong> {{ number_format($selectedComision['total'], 2, ',', '.') }} €</li>
                            <li><strong>Vehicle:</strong> {{ $selectedComision['medio'] }}</li>
                            <li><strong>Kilometratge:</strong> {{ $selectedComision['kilometraje'] }} km</li>
                            @if ($selectedComision['marca'] !== '' || $selectedComision['matricula'] !== '')
                                <li><strong>Vehicle propi:</strong> {{ trim($selectedComision['marca'] . ' ' . $selectedComision['matricula']) }}</li>
                            @endif
                            @if ($selectedComision['itinerario'] !== '')
                                <li><strong>Itinerari:</strong> {{ $selectedComision['itinerario'] }}</li>
                            @endif
                        </ul>
                    @else
                        <p class="text-muted">Carregant comissió...</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Tancar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            if (window.__comisionLivewireUiInit) {
                return;
            }
            window.__comisionLivewireUiInit = true;

            document.addEventListener('livewire:init', function () {
                Livewire.on('show-comision-modal', function () {
                    if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                        window.jQuery('#showComision').modal('show');
                        return;
                    }

                    if (window.bootstrap && window.bootstrap.Modal) {
                        var modal = document.getElementById('showComision');
                        if (modal) {
                            window.bootstrap.Modal.getOrCreateInstance(modal).show();
                        }
                    }
                });
            });
        })();
    </script>
</div>
