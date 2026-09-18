@extends('layouts.intranet')

@section('titulo', 'Detall de la sol·licitud')

@section('content')
<div class="container">
    <h1>Sol·licitud del {{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</h1>
    @foreach ($sollicitud->convalidacions as $peticio)
        <div class="card mb-3"><div class="card-body">
            <h2 class="h5">{{ $peticio->moduloDestino?->literal ?? $peticio->modulo_destino_id }}</h2>
            <p><strong>Origen:</strong> {{ \Intranet\Entities\Convalidacio::origenOptions()[$peticio->origen] ?? $peticio->origen }}</p>
            @if ($peticio->moduloOrigen)<p><strong>Mòdul origen:</strong> {{ $peticio->moduloOrigen->literal }}</p>@endif
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
