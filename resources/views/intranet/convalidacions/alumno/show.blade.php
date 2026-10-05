@extends('layouts.intranet')

@section('titulo', 'Detall de la sol·licitud')

@section('content')
<div class="container">
    @php
        $sollicitudAutomatica = $sollicitud->convalidacions->contains(fn ($item) => filled($item->regla_automatica_id));
    @endphp
    <h1>{{ $sollicitudAutomatica ? 'Sol·licitud amb resolucions automàtiques' : 'Sol·licitud' }} del {{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</h1>
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
                            @if (filled($peticio->regla_automatica_id))
                                <div class="mb-1"><strong>Regla aplicada:</strong> {{ $peticio->regla_automatica_id }}</div>
                                <div class="mb-1"><strong>Resultat:</strong> {{ $peticio->resultat_automatic }}@if ($peticio->mode_nota_automatic === 'preserve') · Nota {{ number_format((float) $peticio->nota_resultat_automatic, 0, ',', '') }}@endif</div>
                                <div class="mb-1"><strong>Base normativa:</strong> {{ collect($peticio->base_normativa_automatica ?? [])->pluck('reference')->filter()->implode(', ') }}</div>
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
                                                {{ $document->descripcio }} — <a href="{{ route('convalidacions.download-attachment', [$peticio, $document]) }}">Descarregar {{ $document->original_name }}</a>
                                                @if ($peticio->estat === \Intranet\Entities\Convalidacio::ESTAT_REVISAR_DOCUMENTACIO && $peticio->esOrigenExtern())
                                                    <form class="d-inline-flex gap-2 mt-1" method="POST" enctype="multipart/form-data" action="{{ route('convalidacions.correct-attachment', [$peticio, $document]) }}">
                                                        @csrf @method('PUT')
                                                        <input class="form-control form-control-sm" type="file" name="fitxer" accept=".pdf,.jpg,.jpeg,.png" required>
                                                        <button class="btn btn-sm btn-warning" type="submit">Substituir</button>
                                                    </form>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif ($peticio->document_path)
                                <div class="mt-2"><a href="{{ route('convalidacions.download', $peticio) }}">Descarregar {{ $peticio->document_original_name }}</a></div>
                            @endif
                            @if ($peticio->document_prl_path)
                                <div class="mt-1"><a href="{{ route('convalidacions.download-prl', $peticio) }}">Descarregar certificat PRL: {{ $peticio->document_prl_original_name }}</a></div>
                            @endif
                            @if ($peticio->estat === \Intranet\Entities\Convalidacio::ESTAT_REVISAR_DOCUMENTACIO)
                                <form class="mt-3" method="POST" enctype="multipart/form-data" action="{{ route('convalidacions.correct', $peticio) }}">
                                    @csrf @method('PUT')
                                    <label class="form-label">Substituïx el document</label>
                                    <input class="form-control mb-2" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <button class="btn btn-warning" type="submit">Enviar correcció</button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <div class="mb-2"><strong>Estat actual:</strong> <span class="badge bg-secondary">{{ \Intranet\Entities\Convalidacio::estatOptions()[$peticio->estat] ?? $peticio->estat }}</span></div>
                            @if ($peticio->observacions)
                                <div><strong>Comentari de Direcció:</strong><div class="alert alert-info mt-1 mb-0">{{ $peticio->observacions }}</div></div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <a class="btn btn-secondary" href="{{ route('convalidacions.index') }}">Tornar</a>
</div>
@endsection
