@extends('layouts.intranet')

@section('titulo', 'Revisió de convalidacions')

@section('content')
<div class="container">
    <h1>Sol·licitud de {{ $sollicitud->alumno?->fullName ?? $sollicitud->alumno_id }}</h1>
    <p>Tramitada el {{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</p>
    @foreach ($sollicitud->convalidacions as $peticio)
        <div class="card mb-3"><div class="card-body">
            <h2 class="h5">{{ $peticio->moduloDestino?->literal ?? $peticio->modulo_destino_id }}</h2>
            <p><strong>Origen:</strong> {{ \Intranet\Entities\Convalidacio::origenOptions()[$peticio->origen] ?? $peticio->origen }}</p>
            @if ($peticio->cicloOrigen)<p><strong>Estudi previ:</strong> {{ $peticio->cicloOrigen->literal }}</p>@endif
            @if ($peticio->document_path)<a href="{{ route('convalidacions.direction.download', $peticio) }}">Descarregar {{ $peticio->document_original_name }}</a>@endif
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
</div>
@endsection
