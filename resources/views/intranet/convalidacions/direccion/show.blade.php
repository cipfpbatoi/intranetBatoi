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
                                <div class="mb-1"><strong>Declaració responsable:</strong> {{ $peticio->declaracio_responsable ? 'Acceptada per a tots els documents adjunts' : 'No acceptada' }}</div>
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
                            @if ($peticio->documents->isNotEmpty())
                                <div class="mt-2"><strong>Documents adjunts</strong>
                                    <ul class="mb-0">
                                        @foreach ($peticio->documents as $document)
                                            <li class="mb-1">
                                                {{ $document->descripcio }} — <a href="{{ route('convalidacions.direction.download-attachment', [$peticio, $document]) }}">{{ $document->original_name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                @if ($peticio->document_path)
                                    <div class="mt-2"><a href="{{ route('convalidacions.direction.download', $peticio) }}">Descarregar {{ $peticio->document_original_name }}</a></div>
                                @endif
                            @endif
                            @if ($peticio->documents->isEmpty() && $peticio->document_prl_path)
                                <div class="mt-1"><strong>Certificat PRL:</strong> <a href="{{ route('convalidacions.direction.download-prl', $peticio) }}">{{ $peticio->document_prl_original_name }}</a></div>
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
                                    <button class="btn btn-primary" type="submit">Guardar esta petició</button>
                                </form>
                                @if ($peticio->revisor)
                                    <div class="mt-2"><small>Últim canvi: {{ $peticio->revisor->fullName ?? $peticio->revisat_per }}, {{ $peticio->revisat_at?->format('d/m/Y H:i') }}</small></div>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
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
