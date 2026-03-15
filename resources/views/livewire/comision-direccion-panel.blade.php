<div>
    @php
        $demà = \Illuminate\Support\Carbon::tomorrow()->setTime(8, 0);
        $formularioComision = new \Intranet\Services\UI\FormBuilder(
            new \Intranet\Entities\Comision([
                'idProfesor' => AuthUser()->dni,
                'desde' => $demà,
                'hasta' => $demà,
                'fct' => 0,
                'servicio' => 'Visita a Empreses: ',
            ]),
            \Intranet\Presentation\Crud\ComisionCrudSchema::FORM_FIELDS
        );
    @endphp

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
                    Imprimir pagaments seleccionats
                    @if (count($selectedPayments) > 0)
                        ({{ count($selectedPayments) }})
                    @endif
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
                Imprimir Comissions autoritzades
            </button>
        @endif

        @if ($hasPendingAuthorization)
            <button type="button" class="btn btn-primary" wire:click="autoritzarPendents">
                Autoritzar comissions pendents
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
                            class="btn btn-warning btn-xs js-edit-comision"
                            data-id="{{ $comision['id'] }}"
                            data-id-profesor="{{ $comision['idProfesor'] }}"
                            data-desde="{{ $comision['desdeEdit'] }}"
                            data-hasta="{{ $comision['hastaEdit'] }}"
                            data-fct="{{ $comision['fct'] }}"
                            data-servicio="{{ $comision['servicio'] }}"
                            data-alojamiento="{{ $comision['alojamiento'] }}"
                            data-comida="{{ $comision['comida'] }}"
                            data-gastos="{{ $comision['gastos'] }}"
                            data-kilometraje="{{ $comision['kilometraje'] }}"
                            data-medio="{{ $comision['medioCodigo'] }}"
                            data-marca="{{ $comision['marca'] }}"
                            data-matricula="{{ $comision['matricula'] }}"
                            data-itinerario="{{ $comision['itinerario'] }}"
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

    <div id="editComision" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                    <h4 class="modal-title">Editar comissió</h4>
                </div>
                {!! $formularioComision->modal() !!}
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

            var editModal = document.getElementById('editComision');
            var editForm = editModal ? editModal.querySelector('form') : null;
            var editMethod = editForm ? editForm.querySelector('#metodo') : null;
            var editId = editForm ? editForm.querySelector('#id') : null;

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

            function setFieldValue(id, value) {
                var field = document.getElementById(id);
                if (!field) {
                    return;
                }

                if ((field.type || '').toLowerCase() === 'checkbox') {
                    field.checked = value === '1' || value === 1 || value === true;
                    return;
                }

                field.value = value == null ? '' : value;
            }

            function updateItinerarioState() {
                var kilometraje = document.getElementById('kilometraje_id');
                var itinerario = document.getElementById('itinerario_id');
                if (!kilometraje || !itinerario) {
                    return;
                }

                var kilometrajeRaw = (kilometraje.value || '').toString().trim().replace(',', '.');
                var kilometrajeValue = Number(kilometrajeRaw);
                var hasValidKilometraje = kilometrajeRaw !== '' && !Number.isNaN(kilometrajeValue) && kilometrajeValue > 0;

                if (kilometraje.disabled || !hasValidKilometraje) {
                    itinerario.value = '';
                    itinerario.disabled = true;
                    return;
                }

                itinerario.disabled = false;
            }

            function updateFctFields() {
                var fct = document.getElementById('fct_id');
                var servicio = document.getElementById('servicio_id');
                var fieldServicio = document.getElementById('field_servicio_id');
                var fieldAlojamiento = document.getElementById('field_alojamiento_id');
                var fieldComida = document.getElementById('field_comida_id');
                var alojamiento = document.getElementById('alojamiento_id');
                var comida = document.getElementById('comida_id');

                if (!fct || !servicio) {
                    updateItinerarioState();
                    return;
                }

                if (fct.checked) {
                    servicio.value = 'Visita empreses FCT:';
                    if (alojamiento) {
                        alojamiento.value = 0;
                    }
                    if (comida) {
                        comida.value = 0;
                    }
                    if (fieldServicio) {
                        fieldServicio.className = 'form-group item hidden';
                    }
                    if (fieldAlojamiento) {
                        fieldAlojamiento.className = 'form-group item hidden';
                    }
                    if (fieldComida) {
                        fieldComida.className = 'form-group item hidden';
                    }
                } else {
                    if (fieldServicio) {
                        fieldServicio.className = 'form-group item';
                    }
                    if (fieldAlojamiento) {
                        fieldAlojamiento.className = 'form-group item';
                    }
                    if (fieldComida) {
                        fieldComida.className = 'form-group item';
                    }
                }

                updateItinerarioState();
            }

            document.addEventListener('livewire:init', function () {
                Livewire.on('show-comision-modal', function () {
                    showModalById('showComision');
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

            document.addEventListener('click', function (event) {
                var button = event.target.closest('.js-edit-comision');
                if (!button || !editForm) {
                    return;
                }

                event.preventDefault();

                editForm.setAttribute('action', '/direccion/comision/' + button.dataset.id + '/edit');
                if (editMethod) {
                    editMethod.value = 'PUT';
                }
                if (editId) {
                    editId.value = button.dataset.id || '';
                }

                setFieldValue('idProfesor_id', button.dataset.idProfesor || '');
                setFieldValue('desde_id', button.dataset.desde || '');
                setFieldValue('hasta_id', button.dataset.hasta || '');
                setFieldValue('fct_id', button.dataset.fct || '0');
                setFieldValue('servicio_id', button.dataset.servicio || '');
                setFieldValue('alojamiento_id', button.dataset.alojamiento || '0');
                setFieldValue('comida_id', button.dataset.comida || '0');
                setFieldValue('gastos_id', button.dataset.gastos || '0');
                setFieldValue('kilometraje_id', button.dataset.kilometraje || '0');
                setFieldValue('medio_id', button.dataset.medio || '0');
                setFieldValue('marca_id', button.dataset.marca || '');
                setFieldValue('matricula_id', button.dataset.matricula || '');
                setFieldValue('itinerario_id', button.dataset.itinerario || '');

                updateFctFields();
                showModalById('editComision');
            });

            document.addEventListener('change', function (event) {
                if (event.target && event.target.id === 'fct_id') {
                    updateFctFields();
                }
            });

            document.addEventListener('input', function (event) {
                if (event.target && event.target.id === 'kilometraje_id') {
                    updateItinerarioState();
                }
            });
        })();
    </script>
</div>
