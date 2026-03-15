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

    @if ($pendingPayments !== [])
        <div class="panel panel-default">
            <div class="panel-heading">
                <button
                    type="button"
                    class="btn btn-link"
                    wire:click="togglePendingPayments"
                    aria-expanded="{{ $paymentsExpanded ? 'true' : 'false' }}"
                    aria-controls="pendingPaymentsPanel"
                    style="display: block; width: 100%; color: inherit; text-align: left; padding: 0; text-decoration: none;"
                >
                    <strong>Pagaments pendents</strong>
                    <span class="pull-right">{{ count($pendingPayments) }} professor(s)</span>
                </button>
            </div>
            <div id="pendingPaymentsPanel" @class(['panel-collapse', 'collapse', 'in' => $paymentsExpanded])>
                <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 60px;">Cobrar</th>
                            <th>Professor</th>
                            <th>Comissions</th>
                            <th>Import pendent</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($pendingPayments as $payment)
                            <tr wire:key="payment-{{ $payment['dni'] }}">
                                <td>
                                    <input
                                        type="checkbox"
                                        value="{{ $payment['dni'] }}"
                                        wire:model.live="selectedPayments"
                                    >
                                </td>
                                <td>{{ $payment['professor'] }}</td>
                                <td>{{ $payment['count'] }}</td>
                                <td>{{ number_format($payment['total'], 2, ',', '.') }} €</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    wire:click="imprimirPagamentsSeleccionats"
                    @disabled($selectedPayments === [])
                >
                    Imprimir pagaments seleccionats ({{ count($selectedPayments) }}/{{ count($pendingPayments) }})
                </button>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-3">
        @if ($hasAuthorizedToPrint)
            <button
                type="button"
                class="btn btn-primary"
                wire:click="imprimirAutoritzades"
            >
                Imprimir Comissions autoritzades ({{ $authorizedToPrintCount }})
            </button>
        @endif

        @if ($hasPendingAuthorization)
            <button type="button" class="btn btn-primary" wire:click="autoritzarPendents">
                Autoritzar comissions pendents ({{ $pendingAuthorizationCount }})
            </button>
        @endif

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

                    @if ($comision['canEdit'])
                        <button
                            type="button"
                            class="btn btn-warning btn-xs"
                            wire:click="editar({{ $comision['id'] }})"
                            title="Editar"
                        >
                            <i class="fa fa-edit" aria-hidden="true"></i>
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

                    @if ($comision['canDelete'])
                        <button
                            type="button"
                            class="btn btn-danger btn-xs"
                            wire:click="esborrar({{ $comision['id'] }})"
                            onclick="return confirm('Segur que vols esborrar esta comissió?');"
                            title="Esborrar"
                        >
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No hi ha comissions per mostrar.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if (isset($paginator) && $paginator->hasPages())
        <div class="text-center">
            {{ $paginator->links() }}
        </div>
    @endif

    <div id="editComision" class="modal fade" role="dialog" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                    <h4 class="modal-title">Editar comissió</h4>
                </div>
                <form wire:submit.prevent="guardarEdicio">
                    <div class="modal-body">
                        <input type="hidden" wire:model="editIdProfesor">

                        <div class="form-group">
                            <label>Professor</label>
                            <input type="text" class="form-control" value="{{ $editProfessorName }}" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_desde">Data i hora d'inici</label>
                                    <input id="edit_desde" type="datetime-local" class="form-control" wire:model.defer="editDesde">
                                    @error('desde') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_hasta">Data i hora de fi</label>
                                    <input id="edit_hasta" type="datetime-local" class="form-control" wire:model.defer="editHasta">
                                    @error('hasta') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" wire:model.live="editFct">
                                FCT
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="edit_servicio">Servei</label>
                            <textarea id="edit_servicio" class="form-control" rows="3" wire:model.defer="editServicio"></textarea>
                            @error('servicio') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        @if (!$editFct)
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_alojamiento">Allotjament</label>
                                        <input id="edit_alojamiento" type="number" step="0.01" min="0" class="form-control" wire:model.defer="editAlojamiento">
                                        @error('alojamiento') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_comida">Menjar</label>
                                        <input id="edit_comida" type="number" step="0.01" min="0" class="form-control" wire:model.defer="editComida">
                                        @error('comida') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_gastos">Altres gastos</label>
                                        <input id="edit_gastos" type="number" step="0.01" min="0" class="form-control" wire:model.defer="editGastos">
                                        @error('gastos') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" wire:model="editAlojamiento">
                            <input type="hidden" wire:model="editComida">
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_kilometraje">Kilometratge</label>
                                    <input id="edit_kilometraje" type="number" min="0" class="form-control" wire:model.live="editKilometraje">
                                    @error('kilometraje') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="edit_medio">Mitjà de transport</label>
                                    <select id="edit_medio" class="form-control" wire:model.defer="editMedio">
                                        @foreach ($medioOptions as $codigo => $label)
                                            <option value="{{ $codigo }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('medio') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        @if (in_array((int) $editMedio, [0, 1], true))
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_marca">Marca</label>
                                        <input id="edit_marca" type="text" class="form-control" maxlength="30" wire:model.defer="editMarca">
                                        @error('marca') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_matricula">Matrícula</label>
                                        <input id="edit_matricula" type="text" class="form-control" maxlength="10" wire:model.defer="editMatricula">
                                        @error('matricula') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="edit_itinerario">Itinerari</label>
                            <textarea
                                id="edit_itinerario"
                                class="form-control"
                                rows="3"
                                wire:model.defer="editItinerario"
                                @disabled((float) str_replace(',', '.', $editKilometraje) <= 0)
                            ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" wire:click="cancelarEditar">Cancel·lar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                    @if ($selectedComision !== null && $selectedComision['hasDocument'])
                        <a
                            class="btn btn-secondary"
                            href="{{ route('comision.direccion.gestor', ['comision' => $selectedComision['id']]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            Veure document
                        </a>
                    @endif
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

            function showModalById(id) {
                if (window.intranetUiHelpers && typeof window.intranetUiHelpers.showModal === 'function') {
                    window.intranetUiHelpers.showModal(id);
                    return;
                }

                if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                    window.jQuery('#' + id).modal('show');
                    return;
                }

                if (window.bootstrap && window.bootstrap.Modal) {
                    var modal = document.getElementById(id);
                    if (modal) {
                        window.bootstrap.Modal.getOrCreateInstance(modal).show();
                    }
                }
            }

            function hideModalById(id) {
                if (window.intranetUiHelpers && typeof window.intranetUiHelpers.hideModal === 'function') {
                    window.intranetUiHelpers.hideModal(id);
                    return;
                }

                if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                    window.jQuery('#' + id).modal('hide');
                    return;
                }

                if (window.bootstrap && window.bootstrap.Modal) {
                    var modal = document.getElementById(id);
                    if (modal) {
                        window.bootstrap.Modal.getOrCreateInstance(modal).hide();
                    }
                }
            }

            document.addEventListener('livewire:init', function () {
                Livewire.on('show-comision-modal', function () {
                    showModalById('showComision');
                });

                Livewire.on('show-edit-comision-modal', function () {
                    showModalById('editComision');
                });

                Livewire.on('hide-edit-comision-modal', function () {
                    hideModalById('editComision');
                });

                Livewire.on('open-report-and-reload', function (event) {
                    var url = event && event.url ? event.url : null;
                    var delay = event && event.delay ? event.delay : 1200;
                    if (!url) {
                        return;
                    }

                    window.open(url, '_blank', 'noopener');
                    window.setTimeout(function () {
                        window.location.reload();
                    }, delay);
                });
            });
        })();
    </script>
</div>
