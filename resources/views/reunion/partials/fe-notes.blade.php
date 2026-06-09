<div class="card reunion-fe-notes-subpoint">
    <a class="d-block collapsed"
       id="headingFeNotes"
       data-bs-toggle="collapse"
       data-bs-parent="#accordion"
       href="#collapseFeNotes"
       aria-expanded="false"
       aria-controls="collapseFeNotes"
    >
        <h4 class="card-title">
            <i class="fa fa-pencil"></i> Apartat 9.1: Notes Formació en Centre
            <small class="text-warning">
                Recordeu que si l'alumnat no ha anat a fer la FE, sols guardarem les notes si ha renunciat firmant en document de renúncia
            </small>
        </h4>
    </a>
    <div id="collapseFeNotes" class="collapse" aria-labelledby="headingFeNotes">
        <div class="card-body">
            <form method="POST" action="{{ route('reunion.feNotes.store', ['reunion' => $formulario->getElemento()->id]) }}">
                {{ csrf_field() }}
                @php
                    $modules = $feNotesData['modulesByStudent']
                        ->flatMap(static fn ($studentModules) => $studentModules)
                        ->unique('id')
                        ->sortBy(static fn ($module) => (string) ($module->Xmodulo ?: $module->id))
                        ->values();
                @endphp
                @if ($modules->isEmpty())
                    <p>No hi ha mòduls associats a l'alumnat.</p>
                @else
                    <div class="form-group">
                        <label for="reunion-fe-student-select">Alumne</label>
                        <select id="reunion-fe-student-select" class="form-control" required>
                            <option value="">Seleccioneu un alumne</option>
                            @foreach ($feNotesData['fcts'] as $fct)
                                @php
                                    $alumno = $fct->Alumno;
                                    $studentName = (string) ($alumno->nameFull ?? $fct->Nombre);
                                @endphp
                                <option value="{{ $fct->idAlumno }}">{{ $studentName }} - {{ $fct->qualificacio }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p class="text-muted">
                        Seleccioneu un alumne per a carregar les notes ja introduïdes i modificar-les. Només es guardarà l'alumne seleccionat.
                    </p>

                    <div class="reunion-fe-student-panels">
                        @foreach ($feNotesData['fcts'] as $fct)
                            @php
                                $alumno = $fct->Alumno;
                                $studentName = (string) ($alumno->nameFull ?? $fct->Nombre);
                                $studentModules = $feNotesData['modulesByStudent']->get((string) $fct->idAlumno, collect());
                            @endphp
                            <div
                                class="reunion-fe-student-panel"
                                data-student-id="{{ $fct->idAlumno }}"
                                data-student-name="{{ $studentName }}"
                                hidden
                            >
                                <h5>
                                    <span class="reunion-fe-student-name">{{ $studentName }}</span>
                                    <small>{{ $fct->qualificacio }}</small>
                                </h5>
                                <label class="reunion-fe-exclude-student">
                                    <input
                                        type="checkbox"
                                        name="excluded_students[]"
                                        value="{{ $fct->idAlumno }}"
                                        class="reunion-fe-exclude-checkbox"
                                        disabled
                                    >
                                    No guardar notes d'este alumne
                                </label>
                                @if ($studentModules->isEmpty())
                                    <p>No hi ha mòduls associats a este alumne.</p>
                                @else
                                    @foreach ($studentModules as $module)
                                        @php
                                            $result = $feNotesData['results']->get($fct->idAlumno . '-' . $module->id);
                                            $selectedNota = (int) ($result->nota ?? 0);
                                            $observaciones = (string) ($result->observaciones ?? '');
                                            $moduleName = (string) ($module->Xmodulo ?: $module->id);
                                        @endphp
                                        <div class="reunion-fe-module-row" data-module-name="{{ $moduleName }}">
                                            <label>{{ $moduleName }}</label>
                                            <select
                                                name="notes[{{ $fct->idAlumno }}][{{ $module->id }}][nota]"
                                                class="form-control reunion-fe-note-select"
                                                disabled
                                            >
                                                @foreach ($feNotesData['gradeOptions'] as $key => $nota)
                                                    <option value="{{ $key }}" @if ($selectedNota === (int) $key) selected @endif>
                                                        {{ $nota }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <textarea
                                                name="notes[{{ $fct->idAlumno }}][{{ $module->id }}][observaciones]"
                                                class="form-control reunion-fe-note-observations"
                                                rows="1"
                                                maxlength="200"
                                                placeholder="Observacions"
                                                disabled
                                            >{{ $observaciones }}</textarea>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="text-center">
                    <input class="btn btn-success mt-2" type="submit" value="Guardar notes FE">
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var studentSelect = document.getElementById('reunion-fe-student-select');
        var panels = document.querySelectorAll('.reunion-fe-student-panel');

        var normalizeText = function (text) {
            return (text || '')
                .toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();
        };

        var findSelectValueForLabel = function (select, label) {
            var normalizedLabel = normalizeText(label);
            var value = '';
            select.querySelectorAll('option').forEach(function (option) {
                if (value !== '') {
                    return;
                }

                if (normalizeText(option.textContent) === normalizedLabel || option.value === label) {
                    value = option.value;
                }
            });

            return value;
        };

        var notesFromSummary = function (studentName) {
            var notes = {};
            var orderRows = document.querySelectorAll('table[name="ordenreunion"] tr.lineaGrupo');
            orderRows.forEach(function (row) {
                var description = row.querySelector('[name="descripcion"]');
                var summary = row.querySelector('[name="resumen"]');
                var normalizedDescription = normalizeText(description ? description.textContent : '');
                if (!summary
                    || (normalizedDescription.indexOf('notes formacio en centre') === -1
                        && normalizedDescription.indexOf('notes reals') === -1)) {
                    return;
                }

                summary.querySelectorAll('ul > li').forEach(function (studentItem) {
                    var moduleList = studentItem.querySelector('ul');
                    var nameNode = studentItem.querySelector('strong');
                    if (!moduleList || !nameNode || normalizeText(nameNode.textContent) !== normalizeText(studentName)) {
                        return;
                    }

                    moduleList.querySelectorAll('li').forEach(function (moduleItem) {
                        var moduleNode = moduleItem.querySelector('strong');
                        if (!moduleNode) {
                            return;
                        }

                        var value = moduleItem.textContent.split(':').pop().trim();
                        notes[normalizeText(moduleNode.textContent)] = value;
                    });
                });
            });

            return notes;
        };

        var applySummaryNotes = function (panel) {
            var summaryNotes = notesFromSummary(panel.getAttribute('data-student-name'));
            if (Object.keys(summaryNotes).length === 0) {
                return;
            }

            panel.querySelectorAll('.reunion-fe-module-row').forEach(function (moduleRow) {
                var select = moduleRow.querySelector('.reunion-fe-note-select');
                var moduleName = normalizeText(moduleRow.getAttribute('data-module-name'));
                if (!select || !summaryNotes[moduleName]) {
                    return;
                }

                var value = findSelectValueForLabel(select, summaryNotes[moduleName]);
                if (value !== '') {
                    select.value = value;
                }
            });
        };

        var setPanelEnabled = function (panel, enabled) {
            panel.hidden = !enabled;
            panel.querySelectorAll('select, textarea, input').forEach(function (field) {
                field.disabled = !enabled;
            });
            if (enabled) {
                var excludeCheckbox = panel.querySelector('.reunion-fe-exclude-checkbox');
                if (excludeCheckbox && excludeCheckbox.checked) {
                    panel.querySelectorAll('select, textarea').forEach(function (field) {
                        field.disabled = true;
                    });
                }
            }
        };

        var updateSelectedStudent = function () {
            var selectedStudent = studentSelect ? studentSelect.value : '';
            panels.forEach(function (panel) {
                var enabled = selectedStudent !== '' && panel.getAttribute('data-student-id') === selectedStudent;
                setPanelEnabled(panel, enabled);
                if (enabled) {
                    applySummaryNotes(panel);
                }
            });
        };

        if (studentSelect) {
            studentSelect.addEventListener('change', updateSelectedStudent);
            updateSelectedStudent();
        }

        document.querySelectorAll('.reunion-fe-exclude-checkbox').forEach(function (checkbox) {
            var updateStudentFields = function () {
                var panel = checkbox.closest('.reunion-fe-student-panel');
                if (!panel || panel.hidden) {
                    return;
                }

                panel.querySelectorAll('select, textarea').forEach(function (field) {
                    field.disabled = checkbox.checked;
                });
            };

            checkbox.addEventListener('change', updateStudentFields);
            updateStudentFields();
        });
    });
</script>
