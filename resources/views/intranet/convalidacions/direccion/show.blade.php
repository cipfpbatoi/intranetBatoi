@extends('layouts.intranet')

@section('titulo', 'Revisió de convalidacions')

@section('content')
<div class="container">
    <h1>Sol·licitud de {{ $sollicitud->alumno?->fullName ?? $sollicitud->alumno_id }}</h1>
    <p>Tramitada el {{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</p>
    @foreach ($sollicitud->convalidacions as $peticio)
        <div class="card mb-3"><div class="card-body">
            <h2 class="h5">{{ $peticio->moduloDestino?->literal ?? $peticio->modulo_destino_id }}</h2>
            <p><strong>Origen:</strong> {{ \Intranet\Entities\Convalidacio::origenLabel($peticio->origen) }}</p>
            @if (!is_null($peticio->fol_logse))
                <p class="mb-1"><strong>Estudis d’origen segons LOGSE:</strong> {{ $peticio->fol_logse ? 'Sí' : 'No' }}</p>
            @endif
            @if (!is_null($peticio->modulo_origen_es_fol))<p class="mb-1"><strong>Mòdul d’origen FOL:</strong> {{ $peticio->modulo_origen_es_fol ? 'Sí' : 'No' }}</p>@endif
            @if ($peticio->origen === \Intranet\Entities\Convalidacio::ORIGEN_ALTRE_CENTRE)<p class="mb-1"><strong>Declaració responsable:</strong> {{ $peticio->declaracio_responsable ? 'Acceptada per a tots els documents adjunts' : 'No acceptada' }}</p>@endif
            @if ($peticio->fol_logse_cicle)<p class="mb-1"><strong>Catàleg FOL:</strong> {{ $peticio->fol_logse_cicle }} ({{ $peticio->fol_logse_nivell }})</p>@endif
            @if ($peticio->ciclo_matricula_id)
                <p class="mb-1"><strong>Cicle de matrícula:</strong> #{{ $peticio->ciclo_matricula_id }} · {{ $peticio->ciclo_matricula_codigo }} — {{ $peticio->ciclo_matricula_nombre_val }} / {{ $peticio->ciclo_matricula_nombre_cas }}</p>
                <p class="mb-1"><strong>Família professional:</strong> Departament #{{ $peticio->departamento_matricula_id }} · {{ $peticio->familia_matricula_nombre_val }} / {{ $peticio->familia_matricula_nombre_cas }} <small class="text-muted">(ITACA {{ $peticio->familia_matricula_codigo_xml }} · {{ $peticio->familia_matricula_abreviatura_xml }})</small></p>
                <p class="mb-1"><strong>Formació del cicle actual:</strong> {{ $peticio->ciclo_matricula_tipo_nombre_val }} / {{ $peticio->ciclo_matricula_tipo_nombre_cas }} · Normativa {{ $peticio->ciclo_matricula_normativa }}</p>
            @endif
            @if ($peticio->modulo_origen_codigo)
                <p class="mb-1"><strong>Mòdul superat:</strong> <strong>{{ preg_replace('/^([A-Za-z]+)(\d+)$/', '$1 $2', $peticio->modulo_origen_codigo) }}@if ($peticio->modulo_origen_nombre) — {{ $peticio->modulo_origen_nombre }}@endif</strong></p>
                <p class="mb-1"><strong>Cicle:</strong> <em>{{ $peticio->ciclo_origen_nombre ?: $peticio->ciclo_origen_codigo }}</em></p>
                @if ($peticio->familia_professional_codigo)
                    <p class="mb-1"><strong>Família professional:</strong> {{ $peticio->familia_professional_codigo }} — {{ $peticio->familia_professional_nombre_val ?: '—' }} / {{ $peticio->familia_professional_nombre_cas ?: '—' }}</p>
                @endif
                @if ($peticio->nivel_origen_codigo)
                    <p class="mb-1"><strong>Nivell formatiu d'origen:</strong> {{ $peticio->nivel_origen_codigo }} — {{ $peticio->nivel_origen_nombre_val ?: '—' }} / {{ $peticio->nivel_origen_nombre_cas ?: '—' }}</p>
                @endif
                <p class="mb-1"><strong>Any d'aprovació:</strong> {{ $peticio->any_origen }}</p>
                <p><strong>Nota:</strong> {{ number_format($peticio->nota_origen, 0, ',', '') }}</p>
            @endif
            @if ($peticio->documents->isNotEmpty())
                <div class="mt-2"><strong>Documents adjunts</strong><ul class="mb-0">
                    @foreach ($peticio->documents as $document)
                        <li>{{ $document->descripcio }} — <a href="{{ route('convalidacions.direction.download-attachment', [$peticio, $document]) }}">{{ $document->original_name }}</a>
                            @if (preg_match('/prl|prevenci[oó]n?.*riesgos/i', $document->descripcio))<div class="small text-muted">Comprova les hores exigides: 30 h per a grau mitjà o 50 h per a grau superior.</div>@endif
                        </li>
                    @endforeach
                </ul></div>
            @else
                @if ($peticio->document_path)<a href="{{ route('convalidacions.direction.download', $peticio) }}">Descarregar {{ $peticio->document_original_name }}</a>@endif
            @endif
            @if ($peticio->documents->isEmpty() && $peticio->document_prl_path)
                <p class="mb-1"><strong>Certificat PRL:</strong> <a href="{{ route('convalidacions.direction.download-prl', $peticio) }}">{{ $peticio->document_prl_original_name }}</a></p>
                <p class="small text-muted">Comprova les hores exigides: 30 h per a grau mitjà o 50 h per a grau superior.</p>
            @endif
            @if ($peticio->revisor)<p class="mt-2"><small>Últim canvi: {{ $peticio->revisor->fullName ?? $peticio->revisat_per }}, {{ $peticio->revisat_at?->format('d/m/Y H:i') }}</small></p>@endif
            @if ($peticio->esTerminal())
                <div class="alert alert-success mb-0">Realitzada — petició de només consulta.</div>
            @else
                <form method="POST" action="{{ route('convalidacions.direction.resolve', $peticio) }}">
                    @csrf @method('PUT')
                    <label class="form-label">Estat</label>
                    <select class="form-select mb-2" name="estat">@foreach ($estats as $value => $label)<option value="{{ $value }}" @selected($peticio->estat === $value)>{{ $label }}</option>@endforeach</select>
                    <label class="form-label">Observació</label>
                    <textarea class="form-control mb-2" name="observacions">{{ $peticio->observacions }}</textarea>
                    <button class="btn btn-primary">Guardar esta petició</button>
                </form>
            @endif
        </div></div>
    @endforeach
    <a class="btn btn-secondary" href="{{ route('convalidacions.direction.index') }}">Tornar</a>
    <form class="d-inline" method="POST" action="{{ route('convalidacions.direction.destroy', $sollicitud) }}" onsubmit="return confirm(&quot;Una sol·licitud d&#39;un alumne no pot eliminar-se llevat que siga una prova, ja que elimina tota la traçabilitat. Vols continuar?&quot;)">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger">Eliminar sol·licitud de prova</button>
    </form>
</div>
@endsection
