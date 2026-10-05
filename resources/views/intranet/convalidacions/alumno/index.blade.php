@extends('layouts.intranet')

@section('titulo', 'Convalidar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Convalidar</h1>
        <a href="{{ route('convalidacions.create') }}" class="btn btn-primary">Nova sol·licitud</a>
    </div>

    @forelse ($sollicituds as $sollicitud)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <span>Tramitada el {{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</span>
                <a href="{{ route('convalidacions.show', $sollicitud) }}">Consultar</a>
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($sollicitud->convalidacions as $peticio)
                    <li class="list-group-item">
                        <strong>{{ $peticio->moduloDestino?->literal ?? $peticio->modulo_destino_id }}</strong>
                        — {{ \Intranet\Entities\Convalidacio::estatOptions()[$peticio->estat] ?? $peticio->estat }}
                        @if ($peticio->observacions)<div class="text-muted">{{ $peticio->observacions }}</div>@endif
                    </li>
                @endforeach
            </ul>
        </div>
    @empty
        <div class="alert alert-info">No tens cap sol·licitud de convalidació.</div>
    @endforelse
</div>
@endsection
