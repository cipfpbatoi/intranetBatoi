<div>
    <h2>Horari del professor {{ $profesorNom }}</h2>

    <div class="mb-3 no-print">
        <label for="propostaSelect"><strong>Proposta</strong></label>
        <select id="propostaSelect" class="form-control" wire:model.live="selectedPropuestaId">
            <option value="">Nova proposta</option>
            @foreach ($propuestaOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @if (!empty($propuestaOptions))
            <small class="text-muted">Selecciona la proposta que vols vore.</small>
        @endif
    </div>

    <p>
        Selecciona una hora ocupada i, després, fes clic a una altra cel·la per a moure-la o intercanviar-la.
        No es pot passar una hora del matí a la vesprada (ni a l'inrevés).
    </p>

    @if ($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if (!$editable)
        <div class="alert alert-warning">Proposta acceptada. No es pot modificar.</div>
    @endif

    @if ($message)
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <div class="mb-3 no-print">
        <strong>Estat:</strong> {{ $estado }}
        <span class="ml-3"><strong>Canvis:</strong> {{ $this->cambiosCount }}</span>
    </div>

    <div class="mb-3 no-print">
        <button class="btn btn-secondary" type="button" wire:click="resetCanvis" @if(!$editable) disabled @endif>
            Desfer canvis
        </button>
        <button class="btn btn-primary" type="button" wire:click="guardarProposta" @if(!$editable) disabled @endif>
            Sol·licitar el canvi
        </button>
        <button class="btn btn-default" type="button" wire:click="novaProposta">
            Nova proposta
        </button>
        <button class="btn btn-danger" type="button" wire:click="esborrarProposta"
            @if(!$propuestaId || in_array($estado, ['Aceptado', 'Guardado'], true)) disabled @endif>
            Esborrar proposta
        </button>
        @php
            $puedeAceptar = $propuestaId && !in_array($estado, ['Aceptado', 'Guardado'], true);
        @endphp
        @if ($isDireccion && $puedeAceptar)
            <a class="btn btn-primary" href="/direccion/horario/propuesta/{{ $dni }}/{{ $propuestaId }}/aceptar"
                onclick="return confirm('Acceptar aquesta proposta?')">
                Acceptar
            </a>
            <button class="btn btn-danger" type="button" onclick="return rebutjarProposta('{{ $dni }}', '{{ $propuestaId }}')">
                Rebutjar
            </button>
        @endif
    </div>

    <div class="mb-3 no-print">
        <label for="obs"><strong>Observacions</strong></label>
        <textarea id="obs" class="form-control" rows="3" wire:model.defer="obs" @if(!$editable) disabled @endif
            placeholder="Indica les tasques concretes que realitzaras amb les hores alliberades"></textarea>
    </div>

    <div class="mb-3 no-print">
        <label for="fecha_inicio"><strong>Data d'inici del canvi</strong></label>
        <input id="fecha_inicio" type="date" class="form-control" wire:model.defer="fechaInicio" @if(!$editable) disabled @endif>
    </div>

    <div class="mb-3 no-print">
        <label for="fecha_fin"><strong>Data de fi del canvi</strong></label>
        <input id="fecha_fin" type="date" class="form-control" wire:model.defer="fechaFin" @if(!$editable) disabled @endif>
    </div>

    <div class="mb-3 no-print">
        <strong>2. DECLARACION RESPONSABLE Y COMPROMISOS (Marcar con X)</strong>
        <div class="checkbox">
            <label>
                <input type="checkbox" wire:model.defer="declaraciones.mantenimiento_turno" @if(!$editable) disabled @endif>
                Mantenimiento de Turno: Las horas se realizaran en el mismo dia y turno original.
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" wire:model.defer="declaraciones.ausencia_alumnado" @if(!$editable) disabled @endif>
                Ausencia de Alumnado: El 100% del alumnado de los modulos indicados esta en empresa.
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" wire:model.defer="declaraciones.servicios_inamovibles" @if(!$editable) disabled @endif>
                Servicios Inamovibles: Mantenimiento de Guardias (Patio/Sala) y Biblioteca.
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" wire:model.defer="declaraciones.atencion_refuerzo" @if(!$editable) disabled @endif>
                Atencion de Refuerzo: Compromiso de atencion presencial si algun alumno lo requiere.
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" wire:model.defer="declaraciones.permanencia" @if(!$editable) disabled @endif>
                Permanencia: El docente permanecera en el centro realizando las tareas indicadas.
            </label>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Hora</th>
                @foreach ($dias as $dia => $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($horas as $hora)
                @php
                    $hasAny = false;
                    foreach ($dias as $dia => $label) {
                        $cellKey = $hora['codigo'] . '-' . $dia;
                        if (isset($grid[$cellKey])) {
                            $hasAny = true;
                            break;
                        }
                    }
                @endphp
                <tr wire:key="hora-{{ $hora['codigo'] }}" class="{{ $hasAny ? '' : 'print-hide-row' }}">
                    <td>
                        {{ $hora['hora_ini'] }} - {{ $hora['hora_fin'] }}
                        @if (!empty($hora['turno']))
                            <div class="text-muted">{{ $hora['turno'] }}</div>
                        @endif
                    </td>
                    @foreach ($dias as $dia => $label)
                        @php
                            $cell = $hora['codigo'] . '-' . $dia;
                            $itemId = $grid[$cell] ?? null;
                            $item = $itemId ? ($items[$itemId] ?? null) : null;
                            $isSelected = $selectedCell === $cell;
                            $isMoved = $item ? ($item['cell'] !== $item['orig']) : false;
                        @endphp
                        <td
                            class="{{ $item ? ($item['tipo'] === 'ocupacio' ? 'warning' : 'active') : '' }} {{ $isSelected ? 'info' : '' }} {{ $editable ? (($item && $item['is_guardia']) ? 'cursor-not-allowed' : 'cursor-pointer') : 'cursor-default' }}"
                            wire:click="cellClicked('{{ $cell }}')"
                            wire:key="cell-{{ $cell }}"
                            data-cell="{{ $cell }}"
                            data-guardia="{{ $item && $item['is_guardia'] ? '1' : '0' }}"
                            draggable="{{ $editable && $item && !$item['is_guardia'] ? 'true' : 'false' }}"
                            ondragstart="horariDragStart(event)"
                            ondragover="horariDragOver(event)"
                            ondrop="horariDrop(event)"
                        >
                            @if ($item)
                                <div class="{{ $isMoved ? 'movido' : '' }}">
                                    <strong>{{ $item['titulo'] }}</strong>
                                    @if (!empty($item['subtitulo']))
                                        <div>{{ $item['subtitulo'] }} @if (!empty($item['aula'])) ({{ $item['aula'] }}) @endif</div>
                                    @elseif (!empty($item['aula']))
                                        <div>{{ $item['aula'] }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">&nbsp;</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <style>
        .no-print {
            display: block;
        }

        .movido {
            color: #b94a48;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .cursor-not-allowed {
            cursor: not-allowed;
        }

        .cursor-default {
            cursor: default;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .print-hide-row {
                display: none !important;
            }

            table {
                margin-top: 0 !important;
            }

            h2 {
                margin-top: 0 !important;
            }

            .left_col,
            .top_nav,
            .nav_menu,
            .footer,
            .sidebar-footer,
            .scroll-view {
                display: none !important;
            }

            .main_container,
            .right_col,
            .container {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            html,
            body {
                height: auto !important;
            }

            @page {
                margin: 12mm;
            }
        }
    </style>
    <script>
        function rebutjarProposta(dni, id) {
            var motiu = prompt('Motiu del rebuig?');
            if (!motiu) return false;
            var url = '/direccion/horario/propuesta/' + dni + '/' + id + '/rebutjar?motiu=' + encodeURIComponent(motiu);
            window.location.href = url;
            return false;
        }

        function horariDragStart(ev) {
            var cell = ev.currentTarget && ev.currentTarget.dataset ? ev.currentTarget.dataset.cell : null;
            if (!cell || ev.currentTarget.getAttribute('draggable') !== 'true') {
                ev.preventDefault();
                return;
            }
            ev.dataTransfer.setData('text/plain', cell);
        }

        function horariDragOver(ev) {
            ev.preventDefault();
        }

        function horariDrop(ev) {
            ev.preventDefault();
            var toCell = ev.currentTarget && ev.currentTarget.dataset ? ev.currentTarget.dataset.cell : null;
            var fromCell = ev.dataTransfer.getData('text/plain');
            if (!toCell || !fromCell) return;
            if (ev.currentTarget.dataset && ev.currentTarget.dataset.guardia === '1') return;

            var root = ev.currentTarget.closest('[wire\\:id]');
            if (!root) return;
            var componentId = root.getAttribute('wire:id');
            if (!componentId || !window.Livewire) return;

            window.Livewire.find(componentId).call('moveFromTo', fromCell, toCell);
        }
    </script>
</div>
