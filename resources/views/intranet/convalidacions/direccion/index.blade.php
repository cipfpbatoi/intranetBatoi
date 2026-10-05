@extends('layouts.intranet')

@section('titulo', 'Gestió de convalidacions')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="mb-0">Gestió de convalidacions</h1>
        <div class="d-flex flex-wrap gap-2">
            @if ($peticionsAutomatiquesElegibles > 0)
                <form method="POST" action="{{ route('convalidacions.direction.rules.apply') }}"
                    data-confirm="S’aplicaran {{ $peticionsAutomatiquesElegibles }} peticions elegibles del catàleg actiu. Les peticions no elegibles continuaran pendents per a revisió manual. No s’actualitzarà ITACA. Vols continuar?"
                    onsubmit="return confirm(this.dataset.confirm)">
                    @csrf
                    <input type="hidden" name="return_to" value="index">
                    <button class="btn btn-primary" type="submit">
                        Aplicar {{ $peticionsAutomatiquesElegibles }} {{ $peticionsAutomatiquesElegibles === 1 ? 'convalidació automàtica' : 'convalidacions automàtiques' }}
                    </button>
                </form>
            @else
                <button class="btn btn-primary" type="button" disabled>Aplicar convalidacions automàtiques</button>
            @endif
            <a class="btn btn-outline-primary" href="{{ route('convalidacions.direction.rules.index') }}">
                <i class="fa fa-check-circle" aria-hidden="true"></i> Gestionar regles automàtiques
            </a>
            <a class="btn btn-outline-primary" href="{{ route('convalidacions.direction.xml.index') }}">
                <i class="fa fa-list-alt" aria-hidden="true"></i> Gestionar avaluacions d'ITACA
            </a>
        </div>
    </div>
    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if ($resultat = session('resultatAutomatic'))
        <div class="alert {{ $resultat['errors'] === [] ? 'alert-success' : 'alert-warning' }}" role="status">
            Convalidacions automàtiques aplicades: <strong>{{ $resultat['aplicats'] }}</strong>.
            Ja existien: <strong>{{ $resultat['ja_existien'] }}</strong>.
            Errors: <strong>{{ count($resultat['errors']) }}</strong>.
            @if ($resultat['errors'] !== [])
                <ul class="mb-0 mt-2">@foreach ($resultat['errors'] as $error)<li>{{ $error['modul'] }} — {{ $error['missatge'] }}</li>@endforeach</ul>
            @endif
        </div>
    @endif
    @if ($accessBlocked)
        <div class="alert alert-warning py-2 mb-2" role="status">
            <strong>Accés restringit per a proves.</strong>
            L’alumnat necessita la contrasenya que s’indica més avall per accedir a les convalidacions.
        </div>
    @endif
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border rounded p-3 mb-3">
        <div>
            <label class="form-label fw-semibold mb-1" for="convalidacions-access-switch">Restringir temporalment l’accés de l’alumnat</label>
            <div class="form-text" id="convalidacions-access-help">Activa-ho durant les proves de la funcionalitat.</div>
            <div class="mt-2">
                <span class="text-muted">Contrasenya per a l’alumnat:</span>
                <code>{{ $accessPassword }}</code>
            </div>
        </div>
        <form method="POST" action="{{ route('convalidacions.direction.access') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="blocked" value="{{ $accessBlocked ? 1 : 0 }}">
            <div class="form-check form-switch mb-0">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    id="convalidacions-access-switch"
                    aria-describedby="convalidacions-access-help"
                    @checked($accessBlocked)
                    onchange="this.form.querySelector('input[name=blocked]').value = this.checked ? '1' : '0'; this.form.requestSubmit()"
                >
                <label class="form-check-label" for="convalidacions-access-switch">
                    {{ $accessBlocked ? 'Bloquejat' : 'Obert' }}
                </label>
            </div>
        </form>
    </div>
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4"><select name="estat" class="form-select"><option value="">Tots els estats</option>@foreach ($estats as $value => $label)<option value="{{ $value }}" @selected(($filters['estat'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4"><select name="origen" class="form-select"><option value="">Tots els orígens</option>@foreach ($origens as $value => $label)<option value="{{ $value }}" @selected(($filters['origen'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4"><button class="btn btn-primary">Filtrar</button> <a class="btn btn-secondary" href="{{ route('convalidacions.direction.index') }}">Netejar</a></div>
    </form>
    <div class="table-responsive"><table class="table table-striped">
        <thead><tr><th>Data</th><th>Alumne</th><th>Peticions</th><th>Automatització</th><th></th></tr></thead>
        <tbody>
            @forelse ($sollicituds as $sollicitud)
                @php($automatitzacio = $automatitzacions[$sollicitud->id] ?? ['pendents' => 0, 'elegibles' => 0])
                <tr>
                    <td>{{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sollicitud->alumno?->fullName ?? $sollicitud->alumno_id }}</td>
                    <td>{{ $sollicitud->convalidacions->count() }}</td>
                    <td>
                        @if ($automatitzacio['pendents'] === 0)
                            <span class="badge bg-secondary">Sense peticions pendents</span>
                        @elseif ($automatitzacio['elegibles'] === 0)
                            <span class="badge bg-secondary">Revisió manual</span>
                        @elseif ($automatitzacio['elegibles'] === $automatitzacio['pendents'])
                            <span class="badge bg-success">Automàtica · {{ $automatitzacio['elegibles'] }}/{{ $automatitzacio['pendents'] }}</span>
                        @else
                            <span class="badge bg-warning text-dark">Parcialment automàtica · {{ $automatitzacio['elegibles'] }}/{{ $automatitzacio['pendents'] }}</span>
                        @endif
                    </td>
                    <td><a href="{{ route('convalidacions.direction.show', $sollicitud) }}">Revisar</a></td>
                </tr>
            @empty
                <tr><td colspan="5">No hi ha sol·licituds amb estos filtres.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endsection
