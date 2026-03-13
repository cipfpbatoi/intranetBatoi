<div>
    @php
        $formularioFalta = new \Intranet\Services\UI\FormBuilder(
            new \Intranet\Entities\Falta([
                'desde' => \Illuminate\Support\Carbon::today(),
                'hasta' => \Illuminate\Support\Carbon::today(),
                'idProfesor' => AuthUser()->dni,
            ]),
            \Intranet\Presentation\Crud\FaltaCrudSchema::FORM_FIELDS
        );
    @endphp

    <h2>Faltes - Pilot Livewire Direcció</h2>

    <p class="text-muted">
        Pilot funcional en convivència amb el panell legacy (<code>/direccion/falta</code>).
    </p>

    @if ($error !== '')
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if ($message !== '')
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <div class="mb-3">
        <button
            id="openCreateFaltaModal"
            type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#createFalta"
            title="Crear nova falta"
        >
            <i class="fa fa-plus" aria-hidden="true"></i>
            Nova
        </button>
        <a class="btn btn-default" href="/direccion/falta">Tornar a versió legacy</a>
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
            <div class="panel-heading"><strong>Rebutjar falta #{{ $rebutjarId }}</strong></div>
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
            <th>Des de</th>
            <th>Fins</th>
            <th>Motiu</th>
            <th>Estat</th>
            <th>Accions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($faltes as $falta)
            <tr wire:key="falta-{{ $falta['id'] }}">
                <td>{{ $falta['id'] }}</td>
                <td>{{ $falta['professor'] }}</td>
                <td>{{ $falta['desde'] }}</td>
                <td>{{ $falta['hasta'] }}</td>
                <td>{{ $falta['motivo'] }}</td>
                <td>{{ $falta['situacion'] }}</td>
                <td>
                    @if (in_array((int) $falta['estado'], [1, 2], true))
                        <button
                            type="button"
                            class="btn btn-warning btn-xs js-edit-falta"
                            data-id="{{ $falta['id'] }}"
                            title="Editar"
                        >
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-danger btn-xs"
                            wire:click="esborrar({{ $falta['id'] }})"
                            onclick="return confirm('Segur que vols esborrar esta falta?');"
                            title="Esborrar"
                        >
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    @endif

                    @if ($falta['estado'] > 0 && $falta['estado'] < 3)
                        <button type="button" class="btn btn-success btn-xs" wire:click="acceptar({{ $falta['id'] }})" title="Acceptar">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                    @endif

                    @if ($falta['estado'] > 0 && $falta['estado'] < 4)
                        <button type="button" class="btn btn-danger btn-xs" wire:click="obrirRebutjar({{ $falta['id'] }})" title="Rebutjar">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    @endif

                    @if ($falta['hasDocument'])
                        <a
                            class="btn btn-info btn-xs"
                            href="{{ route('falta.direccion.document', ['falta' => $falta['id']]) }}"
                            target="_blank"
                            rel="noopener"
                            title="Veure document"
                        >
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        </a>
                    @endif

                    @if ((int) $falta['estado'] === 5)
                        <button type="button" class="btn btn-primary btn-xs" wire:click="alta({{ $falta['id'] }})">
                            Alta
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No hi ha faltes per mostrar.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div id="createFalta" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                    <h4 class="modal-title">Comunicació d'Absència Professorat</h4>
                </div>
                {!! $formularioFalta->modal() !!}
            </div>
        </div>
    </div>

    <script>
        (function () {
            if (window.__faltaLivewireUiInit) {
                return;
            }
            window.__faltaLivewireUiInit = true;

            var createModal = document.getElementById('createFalta');
            if (!createModal) {
                return;
            }

            var form = createModal.querySelector('form');
            if (!form) {
                return;
            }

            var methodInput = form.querySelector('#metodo');
            var idInput = form.querySelector('#id');
            var openButton = document.getElementById('openCreateFaltaModal');

            function showCreateModal() {
                if (window.intranetUiHelpers && typeof window.intranetUiHelpers.showModal === 'function') {
                    window.intranetUiHelpers.showModal('createFalta');
                    return;
                }

                if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                    window.jQuery('#createFalta').modal('show');
                    return;
                }

                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(createModal).show();
                }
            }

            function enhanceProfesorSelect() {
                var select = document.getElementById('idProfesor_id');
                if (!select || select.dataset.searchEnhanced === '1') {
                    return;
                }

                var wrapper = document.createElement('div');
                wrapper.className = 'col-md-7 col-xs-12';
                wrapper.style.marginBottom = '8px';

                var searchInput = document.createElement('input');
                searchInput.type = 'text';
                searchInput.className = 'form-control';
                searchInput.id = 'idProfesor_search';
                searchInput.placeholder = 'Buscar professor per nom, cognoms o DNI';
                searchInput.autocomplete = 'off';

                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(searchInput);

                var allOptions = Array.prototype.slice.call(select.options);

                function normalize(text) {
                    return (text || '')
                        .toString()
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .trim();
                }

                function filterOptions() {
                    var query = normalize(searchInput.value);

                    allOptions.forEach(function (option) {
                        var haystack = normalize(option.text + ' ' + option.value);
                        var visible = query === '' || haystack.indexOf(query) !== -1;

                        option.hidden = !visible;
                        option.disabled = !visible;
                    });
                }

                function syncSearchWithSelection() {
                    var selectedOption = select.options[select.selectedIndex];
                    if (!selectedOption) {
                        return;
                    }

                    searchInput.value = selectedOption.value === '' ? '' : selectedOption.text;
                    filterOptions();
                }

                searchInput.addEventListener('input', filterOptions);

                searchInput.addEventListener('keydown', function (event) {
                    if (event.key !== 'Enter') {
                        return;
                    }

                    var firstVisibleOption = allOptions.find(function (option) {
                        return !option.hidden && !option.disabled && option.value !== '';
                    });

                    if (!firstVisibleOption) {
                        return;
                    }

                    event.preventDefault();
                    select.value = firstVisibleOption.value;
                    syncSearchWithSelection();
                });

                select.addEventListener('change', syncSearchWithSelection);

                select.dataset.searchEnhanced = '1';
                syncSearchWithSelection();
            }

            function prepareCreateForm() {
                form.setAttribute('action', '/direccion/falta');
                if (methodInput) {
                    methodInput.value = 'POST';
                }
                if (idInput) {
                    idInput.value = '';
                }
                enhanceProfesorSelect();
            }

            prepareCreateForm();

            if (openButton) {
                openButton.addEventListener('click', prepareCreateForm);
            }

            function setFieldValue(fieldName, value) {
                var field = document.getElementById(fieldName + '_id');
                if (!field) {
                    return;
                }

                var type = (field.getAttribute('type') || '').toLowerCase();
                if (type === 'checkbox') {
                    field.checked = !!value;
                    return;
                }

                if (value === null || typeof value === 'undefined') {
                    field.value = '';
                    return;
                }

                field.value = value;
            }

            function prepareEditForm(id) {
                var url = '/direccion/falta/' + id + '/edit-data';
                showCreateModal();

                fetch(url, { credentials: 'same-origin' })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }
                        return response.json();
                    })
                    .then(function (res) {
                        var data = res && res.data ? res.data : {};
                        Object.keys(data).forEach(function (key) {
                            setFieldValue(key, data[key]);
                        });

                        form.setAttribute('action', '/direccion/falta/' + id + '/edit');
                        if (methodInput) {
                            methodInput.value = 'PUT';
                        }
                        if (idInput) {
                            idInput.value = id;
                        }

                        enhanceProfesorSelect();
                        var profesorSelect = document.getElementById('idProfesor_id');
                        if (profesorSelect) {
                            profesorSelect.dispatchEvent(new Event('change'));
                        }
                        showCreateModal();
                    })
                    .catch(function (error) {
                        console.error(error);
                        alert("No s'ha pogut carregar la falta per editar.");
                    });
            }

            document.addEventListener('click', function (event) {
                var button = event.target.closest('.js-edit-falta');
                if (!button) {
                    return;
                }

                event.preventDefault();
                var id = button.getAttribute('data-id');
                if (!id) {
                    return;
                }

                prepareEditForm(id);
            });
        })();
    </script>
</div>
