@extends('layouts.intranet')

@section('titulo', 'Detall de la sol·licitud')

@section('content')
<div class="container">
    <h1>Sol·licitud del {{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</h1>
    @foreach ($sollicitud->convalidacions as $peticio)
        <div class="card mb-3"><div class="card-body">
            <h2 class="h5">{{ $peticio->moduloDestino?->literal ?? $peticio->modulo_destino_id }}</h2>
            <p><strong>Origen:</strong> {{ \Intranet\Entities\Convalidacio::origenOptions()[$peticio->origen] ?? $peticio->origen }}</p>
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
            <p><strong>Estat:</strong> {{ \Intranet\Entities\Convalidacio::estatOptions()[$peticio->estat] ?? $peticio->estat }}</p>
            @if ($peticio->observacions)<div class="alert alert-info">{{ $peticio->observacions }}</div>@endif
            @if ($peticio->document_path)
                <a class="btn btn-outline-primary btn-sm" href="{{ route('convalidacions.download', $peticio) }}">Descarregar {{ $peticio->document_original_name }}</a>
            @endif
            @if ($peticio->estat === \Intranet\Entities\Convalidacio::ESTAT_REVISAR_DOCUMENTACIO)
                <form class="mt-3" method="POST" enctype="multipart/form-data" action="{{ route('convalidacions.correct', $peticio) }}">
                    @csrf @method('PUT')
                    <label class="form-label">Substituïx el document</label>
                    <input class="form-control mb-2" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                    <button class="btn btn-warning" type="submit">Enviar correcció</button>
                </form>
            @endif
        </div></div>
    @endforeach
    <a class="btn btn-secondary" href="{{ route('convalidacions.index') }}">Tornar</a>
</div>
@endsection
