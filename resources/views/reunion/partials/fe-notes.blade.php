<div class="card">
    <a class="d-block collapsed"
       id="headingFeNotes"
       data-bs-toggle="collapse"
       data-bs-parent="#accordion"
       href="#collapseFeNotes"
       aria-expanded="false"
       aria-controls="collapseFeNotes"
    >
        <h4 class="card-title"><i class="fa fa-pencil"></i> Notes reals FE</h4>
    </a>
    <div id="collapseFeNotes" class="collapse" aria-labelledby="headingFeNotes">
        <div class="card-body">
            <form method="POST" action="{{ route('reunion.feNotes.store', ['reunion' => $formulario->getElemento()->id]) }}">
                {{ csrf_field() }}
                @foreach ($feNotesData['fcts'] as $fct)
                    @php
                        $alumno = $fct->Alumno;
                        $modules = $feNotesData['modulesByStudent']->get((string) $fct->idAlumno, collect());
                    @endphp
                    <h5>{{ $alumno->nameFull ?? $fct->Nombre }} - {{ $fct->qualificacio }}</h5>
                    @if ($modules->isEmpty())
                        <p>No hi ha mòduls associats a l'alumne.</p>
                    @else
                        <table class="table table-striped table-condensed">
                            <tr>
                                <th style="width: 35%">Mòdul</th>
                                <th style="width: 20%">Nota real</th>
                                <th style="width: 45%">Observacions</th>
                            </tr>
                            @foreach ($modules as $module)
                                @php
                                    $result = $feNotesData['results']->get($fct->idAlumno . '-' . $module->id);
                                    $selectedNota = (int) ($result->nota ?? 0);
                                    $observaciones = (string) ($result->observaciones ?? '');
                                @endphp
                                <tr>
                                    <td>{{ $module->Xmodulo ?: $module->id }}</td>
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
                                    </td>
                                    <td>
                                        <textarea
                                            name="notes[{{ $fct->idAlumno }}][{{ $module->id }}][observaciones]"
                                            class="form-control"
                                            rows="1"
                                            maxlength="200"
                                        >{{ $observaciones }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                @endforeach
                <input class="boton" type="submit" value="Guardar notes FE">
            </form>
        </div>
    </div>
</div>
