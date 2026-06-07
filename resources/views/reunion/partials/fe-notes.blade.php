<div class="card reunion-fe-notes-subpoint">
    <a class="d-block collapsed"
       id="headingFeNotes"
       data-bs-toggle="collapse"
       data-bs-parent="#accordion"
       href="#collapseFeNotes"
       aria-expanded="false"
       aria-controls="collapseFeNotes"
    >
        <h4 class="card-title"><i class="fa fa-pencil"></i> Apartat 9.1: Notes Formació en Centre</h4>
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
                    <div class="reunion-fe-notes-table-wrapper">
                        <table class="table table-striped table-condensed reunion-fe-notes-table">
                            <tr>
                                <th>Alumne</th>
                                @foreach ($modules as $module)
                                    <th>{{ $module->Xmodulo ?: $module->id }}</th>
                                @endforeach
                            </tr>
                            @foreach ($feNotesData['fcts'] as $fct)
                                @php
                                    $alumno = $fct->Alumno;
                                    $studentModules = $feNotesData['modulesByStudent']->get((string) $fct->idAlumno, collect())->pluck('id');
                                @endphp
                                <tr class="reunion-fe-student-row">
                                    <td>
                                        <span class="reunion-fe-student-name">{{ $alumno->nameFull ?? $fct->Nombre }}</span><br>
                                        {{ $fct->qualificacio }}
                                        <label class="reunion-fe-exclude-student">
                                            <input
                                                type="checkbox"
                                                name="excluded_students[]"
                                                value="{{ $fct->idAlumno }}"
                                                class="reunion-fe-exclude-checkbox"
                                            >
                                            No guardar notes d'este alumne
                                        </label>
                                    </td>
                                    @foreach ($modules as $module)
                                        @if ($studentModules->contains($module->id))
                                            @php
                                                $result = $feNotesData['results']->get($fct->idAlumno . '-' . $module->id);
                                                $selectedNota = (int) ($result->nota ?? 0);
                                                $observaciones = (string) ($result->observaciones ?? '');
                                            @endphp
                                            <td>
                                                <select
                                                    name="notes[{{ $fct->idAlumno }}][{{ $module->id }}][nota]"
                                                    class="form-control"
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
                                                >{{ $observaciones }}</textarea>
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
                <input class="btn btn-success mt-2" type="submit" value="Guardar notes FE">
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.reunion-fe-exclude-checkbox').forEach(function (checkbox) {
            var updateStudentFields = function () {
                var row = checkbox.closest('.reunion-fe-student-row');
                if (!row) {
                    return;
                }

                row.querySelectorAll('select, textarea').forEach(function (field) {
                    field.disabled = checkbox.checked;
                });
            };

            checkbox.addEventListener('change', updateStudentFields);
            updateStudentFields();
        });
    });
</script>
