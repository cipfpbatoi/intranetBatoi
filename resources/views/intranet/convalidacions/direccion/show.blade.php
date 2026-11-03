@extends('layouts.intranet')

@section('titulo', 'Revisió de convalidacions')

@section('content')
<div class="container">
    @php
        $sollicitudAutomatica = $sollicitud->convalidacions->contains(fn ($item) => filled($item->regla_automatica_id));
    @endphp
    <h1>{{ $sollicitudAutomatica ? 'Sol·licitud amb resolucions automàtiques' : 'Sol·licitud' }} de {{ $sollicitud->alumno?->fullName ?? $sollicitud->alumno_id }}</h1>
    @if (session('warning'))<div class="alert alert-warning" role="alert">{{ session('warning') }}</div>@endif
    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
    <p>Tramitada el {{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</p>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Mòdul que vol convalidar</th>
                    <th scope="col">Estudis i documents aportats</th>
                    <th scope="col">Estat i comentari</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sollicitud->convalidacions as $peticio)
                    @php
                        $familiaMatricula = $peticio->familia_matricula_nombre_val ?: $peticio->familia_matricula_nombre_cas;
                        $familiaOrigen = $peticio->familia_professional_nombre_val ?: $peticio->familia_professional_nombre_cas;
                        $nivellMatricula = $peticio->ciclo_matricula_tipo_nombre_val ?: $peticio->ciclo_matricula_tipo_nombre_cas;
                        $nivellMatricula = preg_replace('/^(?:Cicle Formatiu(?: de)?|Ciclo Formativo(?: de)?)\s+/u', '', (string) $nivellMatricula);
                        $nivellOrigen = $peticio->nivel_origen_nombre_val ?: $peticio->nivel_origen_nombre_cas;
                    @endphp
                    <tr>
                        <td class="w-25">
                            <div class="mb-2"><strong>{{ $peticio->moduloDestino?->literal ?? $peticio->modulo_destino_id }}</strong></div>
                            @if ($peticio->ciclo_matricula_id)
                                <div class="mb-1"><strong>Cicle de matrícula:</strong> #{{ $peticio->ciclo_matricula_id }} · {{ $peticio->ciclo_matricula_codigo }} — {{ $peticio->ciclo_matricula_nombre_val }} / {{ $peticio->ciclo_matricula_nombre_cas }}</div>
                                <div class="mb-1"><strong>Família professional:</strong> {{ $familiaMatricula }}</div>
                                <div class="mb-1"><strong>Nivell:</strong> {{ $nivellMatricula }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="mb-2"><strong>Origen:</strong> {{ \Intranet\Entities\Convalidacio::origenLabel($peticio->origen) }}</div>
                            @if ($peticio->acreditacio_alternativa)
                                <div class="alert alert-warning py-2 mb-2">
                                    <strong>Acreditació alternativa — revisió manual</strong>
                                    <div>{{ $peticio->acreditacio_descripcio }}</div>
                                    <small>No correspon a un resultat verificat en l’XML.</small>
                                </div>
                            @endif
                            @if (filled($peticio->regla_automatica_id))
                                <div class="mb-1"><strong>Regla aplicada:</strong> {{ $peticio->regla_automatica_id }}</div>
                                <div class="mb-1"><strong>Catàleg:</strong> {{ $peticio->regla_automatica_version }}</div>
                                <div class="mb-1"><strong>Base normativa:</strong> {{ collect($peticio->base_normativa_automatica ?? [])->pluck('reference')->filter()->implode(', ') }}</div>
                            @elseif ($casAutomatic = ($peticionsElegibles[$peticio->id] ?? null))
                                @php($reglaCandidata = $casAutomatic['regla'])
                                <div class="alert alert-info py-2 px-3 mt-2 mb-2">
                                    <strong>Disponible per a resolució automàtica</strong>
                                    <div>Regla: {{ $reglaCandidata['id'] }} — {{ $reglaCandidata['source']['name'] ?? 'Origen' }} → {{ $reglaCandidata['target']['name'] ?? 'Destí' }}</div>
                                    <div>Resultat {{ $reglaCandidata['result']['status'] ?? '—' }} · base normativa {{ collect($reglaCandidata['legal_basis'] ?? [])->pluck('reference')->filter()->implode(', ') }}</div>
                                    @if ($correspondenciaAngles = ($casAutomatic['correspondencia_angles'] ?? null))
                                        <div class="mt-1">Cicle d’anglés: <code>{{ $correspondenciaAngles['codi_cicle_angles'] }}</code> — {{ $correspondenciaAngles['nom_cicle_angles_val'] }} / {{ $correspondenciaAngles['nom_cicle_angles_cas'] }}</div>
                                        <div>Cicle contenidor: <code>{{ $correspondenciaAngles['codi_cicle_contenidor'] }}</code> — {{ $correspondenciaAngles['nom_cicle_contenidor_val'] }} / {{ $correspondenciaAngles['nom_cicle_contenidor_cas'] }} · {{ $correspondenciaAngles['es_grau_superior'] ? 'Grau superior' : 'No és grau superior' }}</div>
                                    @endif
                                </div>
                            @elseif ($peticio->estat !== \Intranet\Entities\Convalidacio::ESTAT_DENEGADA && !$peticio->esTerminal())
                                <div class="small text-muted mb-2">Sense regla automàtica aplicable ara; requerix revisió manual.</div>
                            @endif
                            @if (in_array($peticio->estat, [\Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA, \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA], true) && $peticio->resultat_resolucio)
                                <div class="alert alert-info py-2 px-3 mb-2">
                                    <strong>Resolució:</strong> {{ $peticio->resultat_resolucio }}
                                    @if ($peticio->mode_nota_resolucio === 'preserve' && $peticio->nota_resultat_resolucio !== null)
                                        · <strong>Nota conservada:</strong> {{ number_format((float) $peticio->nota_resultat_resolucio, 0, ',', '') }}
                                    @elseif ($peticio->mode_nota_resolucio === 'none')
                                        · Sense nota
                                    @elseif ($peticio->mode_nota_resolucio === 'preserve')
                                        · Nota d’origen no disponible
                                    @endif
                                </div>
                            @endif
                            @if (!is_null($peticio->fol_logse))
                                <div class="mb-1"><strong>Estudis d’origen segons LOGSE:</strong> {{ $peticio->fol_logse ? 'Sí' : 'No' }}</div>
                            @endif
                            @if (!is_null($peticio->modulo_origen_es_fol))
                                <div class="mb-1"><strong>Mòdul d’origen FOL:</strong> {{ $peticio->modulo_origen_es_fol ? 'Sí' : 'No' }}</div>
                            @endif
                            @if ($peticio->declaracio_responsable || $peticio->documents->isNotEmpty() || $peticio->document_path || $peticio->document_prl_path)
                                <div class="mb-1"><strong>Declaració responsable:</strong> {{ $peticio->declaracio_responsable ? 'Acceptada per als documents aportats per l’alumne' : 'No acceptada' }}</div>
                            @endif
                            @if ($peticio->fol_logse_cicle)
                                <div class="mb-1"><strong>Catàleg FOL:</strong> {{ $peticio->fol_logse_cicle }} ({{ $peticio->fol_logse_nivell }})</div>
                            @endif
                            @if ($peticio->modulo_origen_codigo)
                                <div class="mb-1"><strong>Mòdul superat:</strong> <strong>{{ preg_replace('/^([A-Za-z]+)(\d+)$/', '$1 $2', $peticio->modulo_origen_codigo) }}@if ($peticio->modulo_origen_nombre) — {{ $peticio->modulo_origen_nombre }}@endif</strong></div>
                                <div class="mb-1"><strong>Cicle d’origen:</strong> <em>{{ $peticio->ciclo_origen_nombre ?: $peticio->ciclo_origen_codigo }}</em></div>
                                @if ($peticio->familia_professional_codigo)
                                    <div class="mb-1"><strong>Família professional:</strong> {{ $familiaOrigen ?: '—' }}</div>
                                @endif
                                @if ($peticio->nivel_origen_codigo)
                                    <div class="mb-1"><strong>Nivell:</strong> {{ $nivellOrigen ?: '—' }}</div>
                                @endif
                                <div class="mb-1"><strong>Any d’aprovació:</strong> {{ $peticio->any_origen }}</div>
                                <div class="mb-1"><strong>Nota:</strong> {{ number_format($peticio->nota_origen, 0, ',', '') }}</div>
                            @endif
                            @if ($peticio->documents->isNotEmpty() || $peticio->document_path || $peticio->document_prl_path)
                                <div class="mt-2"><strong>Documents adjunts</strong>
                                    <ul class="mb-0">
                                        @foreach ($peticio->documents as $document)
                                            <li class="mb-1">
                                                {{ $document->descripcio }} — <a href="{{ route('convalidacions.direction.download-attachment', [$peticio, $document]) }}">{{ $document->original_name }}</a>
                                            </li>
                                        @endforeach
                                        @if ($peticio->document_path && !$peticio->documents->contains('path', $peticio->document_path))
                                            <li class="mb-1">Document anterior — <a href="{{ route('convalidacions.direction.download', $peticio) }}">{{ $peticio->document_original_name }}</a></li>
                                        @endif
                                        @if ($peticio->document_prl_path && !$peticio->documents->contains('path', $peticio->document_prl_path))
                                            <li class="mb-1"><strong>Certificat PRL:</strong> <a href="{{ route('convalidacions.direction.download-prl', $peticio) }}">{{ $peticio->document_prl_original_name }}</a></li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                            @if ($peticio->aportacionsDocumentals->isNotEmpty())
                                <div class="mt-3">
                                    <strong>Aportacions de Secretaria o Direcció</strong>
                                    <ul class="mb-0">
                                        @foreach ($peticio->aportacionsDocumentals as $aportacio)
                                            <li class="mb-2">
                                                <div>
                                                    <strong>{{ $aportacio->actor?->fullName ?? $aportacio->actor_dni }}</strong>
                                                    <span class="text-muted">({{ collect($aportacio->rols_actor)->map(fn ($rol) => $rol === 'secretaria' ? 'Secretaria' : 'Direcció')->implode(' / ') }}, {{ $aportacio->created_at?->format('d/m/Y H:i') }})</span>
                                                </div>
                                                @if ($aportacio->path)
                                                    <div>{{ $aportacio->descripcio }} — <a href="{{ route('convalidacions.direction.download-staff-contribution', [$peticio, $aportacio]) }}">{{ $aportacio->original_name }}</a></div>
                                                @endif
                                                @if ($aportacio->comentari)
                                                    <div>{{ $aportacio->comentari }}</div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($peticio->document_prl_path)
                                <div class="small text-muted">Comprova les hores exigides: 30 h per a grau mitjà o 50 h per a grau superior.</div>
                            @endif
                            @if ($peticio->modulo_destino_id === '1709' && $peticio->modulo_origen_es_fol && $peticio->fol_logse)
                                <div class="small text-muted">Comprova que la documentació incloga el certificat de Prevenció de Riscos Laborals i les hores exigides: 30 h per a grau mitjà o 50 h per a grau superior.</div>
                            @endif
                        </td>
                        <td>
                            @if ($peticio->esTerminal())
                                <div class="alert alert-success mb-0">{{ $estats[$peticio->estat] ?? $peticio->estat }} — registre de només consulta.</div>
                            @else
                                <div class="mb-2"><strong>Estat actual:</strong> <span class="badge bg-secondary">{{ $estats[$peticio->estat] ?? $peticio->estat }}</span></div>
                                @php($estatSeleccionat = old('estat', $peticio->estat))
                                <form method="POST" action="{{ route('convalidacions.direction.resolve', $peticio) }}">
                                    @csrf @method('PUT')
                                    <label class="form-label" for="estat-{{ $peticio->id }}">Nou estat</label>
                                    <select class="form-select mb-2" id="estat-{{ $peticio->id }}" name="estat">@foreach (\Intranet\Entities\Convalidacio::estatManualOptions() as $value => $label)<option value="{{ $value }}" @selected($estatSeleccionat === $value)>{{ $label }}</option>@endforeach @if ($peticio->estat === \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA)<option value="{{ \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA }}" @selected($estatSeleccionat === \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA)>Acceptada (Convalidada)</option>@endif</select>
                                    <div id="resolucio-{{ $peticio->id }}" @if ($estatSeleccionat !== \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA) hidden @endif>
                                        <label class="form-label" for="resultat-resolucio-{{ $peticio->id }}">Resultat de la convalidació</label>
                                        <select class="form-select mb-2" id="resultat-resolucio-{{ $peticio->id }}" name="resultat_resolucio" @required($estatSeleccionat === \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA)>
                                            <option value="">Selecciona CO o AA</option>
                                            <option value="CO" @selected(old('resultat_resolucio', $peticio->resultat_resolucio) === 'CO')>CO</option>
                                            <option value="AA" @selected(old('resultat_resolucio', $peticio->resultat_resolucio) === 'AA')>AA</option>
                                        </select>
                                        <label class="form-label" for="mode-nota-resolucio-{{ $peticio->id }}">Tractament de la nota</label>
                                        <select class="form-select mb-2" id="mode-nota-resolucio-{{ $peticio->id }}" name="mode_nota_resolucio" @required($estatSeleccionat === \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA)>
                                            <option value="">Selecciona què fer amb la nota</option>
                                            <option value="preserve" @selected(old('mode_nota_resolucio', $peticio->mode_nota_resolucio) === 'preserve')>Conservar la nota que consta en la documentació</option>
                                            <option value="none" @selected(old('mode_nota_resolucio', $peticio->mode_nota_resolucio) === 'none')>La resolució no porta nota</option>
                                        </select>
                                        @php($notaResolucio = old('nota_resultat_resolucio', $peticio->nota_resultat_resolucio ?? $peticio->nota_origen))
                                        <div id="nota-resolucio-container-{{ $peticio->id }}" @if (old('mode_nota_resolucio', $peticio->mode_nota_resolucio) !== 'preserve') hidden @endif>
                                            <label class="form-label" for="nota-resultat-resolucio-{{ $peticio->id }}">Nota acreditada</label>
                                            <input class="form-control mb-1" type="number" id="nota-resultat-resolucio-{{ $peticio->id }}" name="nota_resultat_resolucio" min="5" max="10" step="1" value="{{ $notaResolucio }}" placeholder="De 5 a 10">
                                            <div class="form-text mb-2">Transcriu la nota que consta en la documentació. Ha de ser un enter entre 5 i 10.</div>
                                        </div>
                                    </div>
                                    <label class="form-label" for="observacions-{{ $peticio->id }}">Comentari de Direcció</label>
                                    <textarea class="form-control mb-2" id="observacions-{{ $peticio->id }}" name="observacions" rows="4">{{ $peticio->observacions }}</textarea>
                                    <div class="form-text mb-2">Si canvies l’estat, l’alumne rebrà un correu encara que no afegisques cap comentari nou. Si guardes sense canvis, no s’enviarà cap avís nou.</div>
                                    <button class="btn btn-primary" type="submit">Guardar esta petició</button>
                                </form>
                                @if ($peticio->revisor)
                                    <div class="mt-2"><small>Últim canvi: {{ $peticio->revisor->fullName ?? $peticio->revisat_per }}, {{ $peticio->revisat_at?->format('d/m/Y H:i') }}</small></div>
                                @endif
                            @endif
                            @if ($potAportarDocumentacio[$peticio->id] ?? false)
                                <div class="mt-3">
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#aportacio-documental-{{ $peticio->id }}">Afegir documentació rebuda</button>
                                    @if (($espaisAportacioDocumental[$peticio->id] ?? 0) > 0)
                                        <div class="form-text mt-1">Pots afegir fins a {{ $espaisAportacioDocumental[$peticio->id] }} documents més. Si n’adjuntes, la petició tornarà a «En procés».</div>
                                    @else
                                        <div class="form-text mt-1">Ja s’ha arribat al màxim de tres documents; encara pots afegir un comentari.</div>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @foreach ($sollicitud->convalidacions as $peticio)
        @if ($potAportarDocumentacio[$peticio->id] ?? false)
            <div class="modal fade" id="aportacio-documental-{{ $peticio->id }}" tabindex="-1" aria-labelledby="aportacio-documental-titol-{{ $peticio->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('convalidacions.direction.staff-contribution', $peticio) }}" data-aportacio-form>
                            @csrf
                            <div class="modal-header">
                                <h2 class="modal-title fs-5" id="aportacio-documental-titol-{{ $peticio->id }}">Afegir documentació rebuda</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tancar"></button>
                            </div>
                            <div class="modal-body">
                                @php($espaisDocuments = $espaisAportacioDocumental[$peticio->id] ?? 0)
                                <p>Identifica cada fitxer amb una descripció, per exemple «Certificat acadèmic» o «Certificat PRL». El comentari és una nota general opcional sobre l’entrega. Els documents anteriors es conservaran. Si adjuntes fitxers, la petició tornarà a «En procés».</p>
                                <div class="alert alert-danger py-2" data-aportacio-feedback role="alert" hidden></div>
                                @if ($espaisDocuments > 0)
                                    <div data-aportacio-documents data-max-files="{{ $espaisDocuments }}">
                                        <div class="mb-2" data-aportacio-document-list>
                                            <div class="border rounded p-2 mb-2" data-aportacio-document>
                                                <label class="form-label" for="descripcio-aportacio-{{ $peticio->id }}-0">Descripció del document</label>
                                                <input class="form-control mb-1" id="descripcio-aportacio-{{ $peticio->id }}-0" name="documents[0][descripcio]" maxlength="120" placeholder="Ex.: Certificat acadèmic" data-aportacio-descripcio>
                                                <div class="invalid-feedback" data-descripcio-error>Indica què acredita este fitxer, per exemple «Certificat PRL».</div>
                                                <label class="form-label mt-2" for="fitxer-aportacio-{{ $peticio->id }}-0">Fitxer (PDF, JPG o PNG)</label>
                                                <input class="form-control" type="file" id="fitxer-aportacio-{{ $peticio->id }}-0" name="documents[0][fitxer]" accept=".pdf,.jpg,.jpeg,.png" data-aportacio-fitxer>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary mb-3" type="button" data-afegir-document @if ($espaisDocuments < 2) hidden @endif>+ Afegir un altre document</button>
                                    </div>
                                @else
                                    <div class="alert alert-info py-2">Ja s’ha arribat al màxim de tres documents d’esta petició. Pots afegir un comentari.</div>
                                @endif
                                <label class="form-label" for="comentari-aportacio-{{ $peticio->id }}">Comentari general <span class="text-muted">(opcional)</span></label>
                                <div class="form-text mb-1">Afig context sobre l’entrega; no és la descripció dels fitxers.</div>
                                <textarea class="form-control" id="comentari-aportacio-{{ $peticio->id }}" name="comentari" rows="3" maxlength="2000"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel·lar</button>
                                <button class="btn btn-primary" type="submit">Afegir a l’expedient</button>
                            </div>
                        </form>
                        @if ($espaisDocuments > 1)
                            <template data-aportacio-document-template>
                                <div class="border rounded p-2 mb-2" data-aportacio-document>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label" for="descripcio-aportacio-{{ $peticio->id }}-__INDEX__">Descripció del document</label>
                                        <button class="btn btn-sm btn-link text-danger" type="button" data-eliminar-document>Eliminar</button>
                                    </div>
                                    <input class="form-control mb-1" id="descripcio-aportacio-{{ $peticio->id }}-__INDEX__" name="documents[__INDEX__][descripcio]" maxlength="120" placeholder="Ex.: Certificat PRL" data-aportacio-descripcio>
                                    <div class="invalid-feedback" data-descripcio-error>Indica què acredita este fitxer, per exemple «Certificat PRL».</div>
                                    <label class="form-label mt-2" for="fitxer-aportacio-{{ $peticio->id }}-__INDEX__">Fitxer (PDF, JPG o PNG)</label>
                                    <input class="form-control" type="file" id="fitxer-aportacio-{{ $peticio->id }}-__INDEX__" name="documents[__INDEX__][fitxer]" accept=".pdf,.jpg,.jpeg,.png" data-aportacio-fitxer>
                                </div>
                            </template>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    <a class="btn btn-secondary" href="{{ route('convalidacions.direction.index') }}">Tornar</a>
    @unless ($sollicitudAutomatica)
        <form class="d-inline" method="POST" action="{{ route('convalidacions.direction.destroy', $sollicitud) }}" onsubmit="return confirm(&quot;Una sol·licitud d&#39;un alumne no pot eliminar-se llevat que siga una prova, ja que elimina tota la traçabilitat. Vols continuar?&quot;)">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Eliminar sol·licitud de prova</button>
        </form>
    @endunless
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-aportacio-form]').forEach(function (form) {
            const documentArea = form.querySelector('[data-aportacio-documents]');
            const feedback = form.querySelector('[data-aportacio-feedback]');
            const comment = form.querySelector('textarea[name="comentari"]');
            if (!documentArea) {
                form.addEventListener('submit', function (event) {
                    if (!comment.value.trim()) {
                        event.preventDefault();
                        feedback.textContent = 'Afig almenys un document o un comentari.';
                        feedback.hidden = false;
                        comment.focus();
                    }
                });
                return;
            }

            const list = documentArea.querySelector('[data-aportacio-document-list]');
            const addButton = documentArea.querySelector('[data-afegir-document]');
            const maxFiles = Number(documentArea.dataset.maxFiles);
            const template = form.parentElement.querySelector('template[data-aportacio-document-template]');
            let nextIndex = list.querySelectorAll('[data-aportacio-document]').length;
            const updateAddButton = function () {
                addButton.hidden = list.querySelectorAll('[data-aportacio-document]').length >= maxFiles;
            };

            if (template) {
                addButton.addEventListener('click', function () {
                    const index = nextIndex++;
                    const row = document.createElement('div');
                    row.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(index));
                    const entry = row.firstElementChild;
                    entry.querySelector('[data-eliminar-document]').addEventListener('click', function () {
                        entry.remove();
                        updateAddButton();
                    });
                    list.append(entry);
                    updateAddButton();
                    entry.querySelector('[data-aportacio-descripcio]').focus();
                });
            }

            form.addEventListener('submit', function (event) {
                let invalidDescription = null;
                let hasFile = false;
                documentArea.querySelectorAll('[data-aportacio-document]').forEach(function (entry) {
                    const file = entry.querySelector('[data-aportacio-fitxer]');
                    const description = entry.querySelector('[data-aportacio-descripcio]');
                    const error = entry.querySelector('[data-descripcio-error]');
                    const selected = file.files.length > 0;
                    hasFile = hasFile || selected;
                    const missing = selected && !description.value.trim();
                    description.classList.toggle('is-invalid', missing);
                    description.setAttribute('aria-invalid', missing ? 'true' : 'false');
                    error.hidden = !missing;
                    if (missing && invalidDescription === null) {
                        invalidDescription = description;
                    }
                });

                if (invalidDescription) {
                    event.preventDefault();
                    invalidDescription.focus();
                    return;
                }
                if (!hasFile && !comment.value.trim()) {
                    event.preventDefault();
                    feedback.textContent = 'Afig almenys un document o un comentari.';
                    feedback.hidden = false;
                    comment.focus();
                }
            });

            form.addEventListener('input', function (event) {
                if (event.target.matches('[data-aportacio-descripcio]')) {
                    event.target.classList.remove('is-invalid');
                    event.target.setAttribute('aria-invalid', 'false');
                    event.target.nextElementSibling.hidden = true;
                }
                if (event.target.matches('textarea[name="comentari"]')) {
                    feedback.hidden = true;
                }
            });
            updateAddButton();
        });

        document.querySelectorAll('select[id^="estat-"]').forEach(function (estat) {
            const peticioId = estat.id.replace('estat-', '');
            const campsResolucio = document.getElementById('resolucio-' + peticioId);
            const controls = campsResolucio.querySelectorAll('select');
            const modeNota = document.getElementById('mode-nota-resolucio-' + peticioId);
            const notaContainer = document.getElementById('nota-resolucio-container-' + peticioId);
            const notaInput = document.getElementById('nota-resultat-resolucio-' + peticioId);
            const actualitzarCampsResolucio = function () {
                const esAcceptada = estat.value === @json(\Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA);
                campsResolucio.hidden = !esAcceptada;
                controls.forEach(function (control) {
                    control.required = esAcceptada;
                    control.disabled = !esAcceptada;
                });
                const conservaNota = esAcceptada && modeNota.value === 'preserve';
                notaContainer.hidden = !conservaNota;
                notaInput.required = conservaNota;
                notaInput.disabled = !conservaNota;
            };
            estat.addEventListener('change', actualitzarCampsResolucio);
            modeNota.addEventListener('change', actualitzarCampsResolucio);
            actualitzarCampsResolucio();
        });
    });
</script>
@endsection
