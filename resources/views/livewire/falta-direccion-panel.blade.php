<div>
    @if ($error !== '')
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if ($message !== '')
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <div class="mb-3">
        <button
            type="button"
            class="btn btn-primary"
            wire:click="crear"
            title="Crear nova falta"
        >
            <i class="fa fa-plus" aria-hidden="true"></i>
            Nova
        </button>
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
        <div class="card border-secondary" style="margin-top: 20px;">
            <div class="card-header"><strong>Rebutjar falta #{{ $rebutjarId }}</strong></div>
            <div class="card-body">
                <label for="motiuRebutjar">Motiu</label>
                <textarea id="motiuRebutjar" class="form-control" wire:model.defer="motiuRebutjar"></textarea>
                <div class="mt-2" style="margin-top: 10px;">
                    <button class="btn btn-danger" type="button" wire:click="confirmarRebutjar">Confirmar rebuig</button>
                    <button class="btn btn-secondary" type="button" wire:click="cancelarRebutjar">Cancel·lar</button>
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
                    <button
                        type="button"
                        class="btn btn-warning btn-xs"
                        wire:click="editar({{ $falta['id'] }})"
                        title="Editar"
                    >
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </button>

                    @if (in_array((int) $falta['estado'], [0, 1, 2], true))
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

    <div id="faltaFormModal" class="modal fade" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="guardar">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="cancelForm"></button>
                        <h5 class="modal-title">
                            {{ $isEditing ? "Editar absència del professorat" : "Comunicació d'Absència Professorat" }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12" style="margin-bottom: 12px;">
                                <label for="formProfessorSearch"><strong>Professor *</strong></label>
                                <input
                                    id="formProfessorSearch"
                                    type="text"
                                    class="form-control"
                                    list="modalProfessorSuggestions"
                                    placeholder="Escriu nom, cognoms o DNI"
                                    wire:model.live.debounce.300ms="formProfessorSearch"
                                >
                                <datalist id="modalProfessorSuggestions">
                                    @foreach ($professorOptions as $professorLabel)
                                        <option value="{{ $professorLabel }}"></option>
                                    @endforeach
                                </datalist>
                                @error('formIdProfesor') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6" style="margin-bottom: 12px;">
                                <label for="formDesde"><strong>Data d'inici *</strong></label>
                                <input id="formDesde" type="date" class="form-control" wire:model.defer="formDesde">
                                @error('formDesde') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6" style="margin-bottom: 12px;">
                                <label for="formHasta"><strong>Data de fi</strong></label>
                                <input id="formHasta" type="date" class="form-control" wire:model.defer="formHasta">
                                @error('formHasta') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6" style="margin-bottom: 12px;">
                                <label>
                                    <input type="checkbox" wire:model.live="formBaja">
                                    Baixa llarga durada
                                </label>
                            </div>
                            <div class="col-md-6" style="margin-bottom: 12px;">
                                <label>
                                    <input type="checkbox" wire:model.live="formDiaCompleto">
                                    Tot el dia
                                </label>
                            </div>
                            @if (!$formBaja && !$formDiaCompleto)
                                <div class="col-md-6" style="margin-bottom: 12px;">
                                    <label for="formHoraIni"><strong>Hora inici *</strong></label>
                                    <input id="formHoraIni" type="time" class="form-control" wire:model.defer="formHoraIni">
                                    @error('formHoraIni') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6" style="margin-bottom: 12px;">
                                    <label for="formHoraFin"><strong>Hora fi *</strong></label>
                                    <input id="formHoraFin" type="time" class="form-control" wire:model.defer="formHoraFin">
                                    @error('formHoraFin') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            @endif
                            <div class="col-md-12" style="margin-bottom: 12px;">
                                <label for="formMotivos"><strong>Motiu *</strong></label>
                                <select id="formMotivos" class="form-control" wire:model.defer="formMotivos">
                                    <option value="">-Selecciona Motiu de l'absència-</option>
                                    @foreach (\Intranet\Entities\Falta::getMotivosOptions() as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('formMotivos') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12" style="margin-bottom: 12px;">
                                <label for="formObservaciones"><strong>Observacions</strong></label>
                                <input id="formObservaciones" type="text" class="form-control" wire:model.defer="formObservaciones">
                                @error('formObservaciones') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="formFichero"><strong>Justificant</strong></label>
                                <input id="formFichero" type="file" class="form-control" wire:model="formFichero">
                                @error('formFichero') <span class="text-danger">{{ $message }}</span> @enderror
                                @if ($existingFichero !== '')
                                    <p class="text-muted" style="margin-top: 8px;">
                                        Ja hi ha un document associat. Pots pujar-ne un altre si vols substituir-lo.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="cancelForm">Cancel·lar</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditing ? 'Guardar canvis' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            if (window.__faltaLivewireUiInit) {
                return;
            }
            window.__faltaLivewireUiInit = true;

            function showModalById(id) {
                if (window.intranetUiHelpers && typeof window.intranetUiHelpers.showModal === 'function') {
                    window.intranetUiHelpers.showModal(id);
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

                if (window.bootstrap && window.bootstrap.Modal) {
                    var modal = document.getElementById(id);
                    if (modal) {
                        window.bootstrap.Modal.getOrCreateInstance(modal).hide();
                    }
                }
            }

            document.addEventListener('livewire:init', function () {
                Livewire.on('show-falta-modal', function () {
                    showModalById('faltaFormModal');
                });

                Livewire.on('hide-falta-modal', function () {
                    hideModalById('faltaFormModal');
                });
            });
        })();
    </script>
</div>
