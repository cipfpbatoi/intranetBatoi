@extends('layouts.intranet')

@section('titulo', 'Convalidacions per mòdul')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="mb-1">Convalidacions per mòdul</h1>
            <p class="text-muted mb-0">Cada fila és una petició individual. El canvi d’estat no afecta la resta de mòduls de la sol·licitud.</p>
        </div>
        <div class="d-flex gap-2">
            @if (esRol((int) auth('profesor')->user()->rol, (int) config('roles.rol.direccion')))
                <a class="btn btn-outline-secondary" href="{{ route('convalidacions.direction.index') }}">Sol·licituds</a>
            @endif
            <a class="btn btn-outline-primary" href="{{ route('convalidacions.direction.modules.pdf', request()->query()) }}" target="_blank" rel="noopener">Descarregar PDF</a>
        </div>
    </div>

    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if (session('warning'))<div class="alert alert-warning" role="status">{{ session('warning') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif

    <form method="GET" action="{{ route('convalidacions.direction.modules.index') }}" class="row g-2 mb-3">
        <div class="col-md-3"><label class="form-label" for="filter-estat">Estat</label><select id="filter-estat" name="estat" class="form-select"><option value="">Tots els estats</option>@foreach ($estats as $value => $label)<option value="{{ $value }}" @selected(($filters['estat'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="filter-origen">Origen</label><select id="filter-origen" name="origen" class="form-select"><option value="">Tots els orígens</option>@foreach ($origens as $value => $label)<option value="{{ $value }}" @selected(($filters['origen'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label" for="filter-nia">NIA</label><input id="filter-nia" name="nia" value="{{ $filters['nia'] ?? '' }}" class="form-control"></div>
        <div class="col-md-2"><label class="form-label" for="filter-modul">Codi del mòdul</label><input id="filter-modul" name="modul" value="{{ $filters['modul'] ?? '' }}" class="form-control"></div>
        <div class="col-md-2 d-flex align-items-end gap-2"><button class="btn btn-primary">Filtrar</button><a class="btn btn-secondary" href="{{ route('convalidacions.direction.modules.index') }}">Netejar</a></div>
    </form>

    <form method="POST" action="{{ route('convalidacions.direction.modules.register-accepted') }}" onsubmit="return confirm('Es marcaran com a Acceptada (Convalidada) únicament els mòduls seleccionats que continuen en estat Acceptada. L’alumnat rebrà un correu individual. Vols continuar?')">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span>{{ $peticions->count() }} {{ $peticions->count() === 1 ? 'mòdul' : 'mòduls' }}</span>
            <button type="submit" class="btn btn-success">Registrar com a convalidats en ITACA</button>
        </div>
        <div class="table-responsive"><table class="table table-striped align-middle">
            <thead><tr><th scope="col">Selecció</th><th scope="col">Alumne</th><th scope="col">Mòdul que vol convalidar</th><th scope="col">Origen i mòdul aportat</th><th scope="col">Estat</th><th scope="col">Sol·licitud</th><th scope="col">Data</th></tr></thead>
            <tbody>
                @forelse ($peticions as $peticio)
                    <tr>
                        <td>@if ($peticio->estat === \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA)<input class="form-check-input" type="checkbox" name="peticions[]" value="{{ $peticio->id }}" aria-label="Selecciona {{ $peticio->moduloDestino?->vliteral ?? $peticio->modulo_destino_id }} per a registrar-la en ITACA">@else<span class="text-muted">—</span>@endif</td>
                        <td>{{ $peticio->sollicitud?->alumno?->fullName ?? $peticio->sollicitud?->alumno_id }}<br><small class="text-muted">NIA {{ $peticio->sollicitud?->alumno_id }}</small></td>
                        <td><strong>{{ $peticio->moduloDestino?->vliteral ?? $peticio->modulo_destino_id }}</strong><br><small class="text-muted">{{ $peticio->modulo_destino_id }}</small></td>
                        <td>{{ \Intranet\Entities\Convalidacio::origenLabel($peticio->origen) }}<br>@if ($peticio->modulo_origen_codigo || $peticio->modulo_origen_nombre)<small>{{ $peticio->modulo_origen_codigo }} — {{ $peticio->modulo_origen_nombre }}</small>@endif</td>
                        <td><span class="badge bg-secondary">{{ $estats[$peticio->estat] ?? $peticio->estat }}</span></td>
                        <td>#{{ $peticio->sollicitud_convalidacio_id }}</td>
                        <td>{{ optional($peticio->sollicitud?->submitted_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No hi ha peticions amb estos filtres.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </form>
</div>
@endsection
