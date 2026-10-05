@extends('layouts.intranet')

@section('titulo', 'Regles automàtiques de convalidació')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="mb-1">Regles de convalidació</h1>
            <p class="mb-0">Les regles coincidents poden resoldre peticions ja presentades. No s'envien dades a ITACA.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('convalidacions.direction.index') }}">Tornar a convalidacions</a>
    </div>

    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if ($resultat = session('resultatAutomatic'))
        <div class="alert {{ $resultat['errors'] === [] ? 'alert-success' : 'alert-warning' }}" role="status">
            Aplicades: <strong>{{ $resultat['aplicats'] }}</strong>.
            Ja existien: <strong>{{ $resultat['ja_existien'] }}</strong>.
            Errors: <strong>{{ count($resultat['errors']) }}</strong>.
            @if ($resultat['errors'] !== [])
                <ul class="mb-0 mt-2">@foreach ($resultat['errors'] as $error)<li>{{ $error['modul'] }} — {{ $error['missatge'] }}</li>@endforeach</ul>
            @endif
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><strong>Catàleg de regles</strong></div>
        <div class="card-body">
            @if ($preview['catalog']['version'])
                <p>
                    <strong>{{ $preview['catalog']['titol'] }}</strong>
                    · versió {{ $preview['catalog']['version'] }}
                    · {{ $preview['catalog']['origen'] === 'carregat' ? 'catàleg carregat per Direcció' : 'catàleg inicial de l’aplicació' }}
                    · actualitzat {{ date('d/m/Y H:i', $preview['catalog']['actualitzat']) }}
                </p>
            @else
                <div class="alert alert-warning">Encara no hi ha cap catàleg de regles disponible. Carrega un fitxer YAML per començar.</div>
            @endif
            <form method="POST" action="{{ route('convalidacions.direction.rules.store') }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label" for="yaml-regles">Carregar o substituir el YAML de regles</label>
                <input class="form-control mb-2" id="yaml-regles" name="yaml" type="file" accept=".yaml,.yml" required>
                <div class="form-text mb-2">Màxim 2 MB. El nou catàleg només s'activa si té una estructura vàlida; el fitxer es guarda en privat.</div>
                <button class="btn btn-outline-primary" type="submit">Validar i carregar catàleg</button>
            </form>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <h2 class="h4 mb-0">Aplicabilitat de les regles</h2>
        @if ($preview['casos'] !== [])
            <form method="POST" action="{{ route('convalidacions.direction.rules.apply') }}" onsubmit="return confirm('S’aplicaran {{ count($preview['casos']) }} convalidacions i quedaran en estat Resolta. No s’actualitzarà ITACA. Vols continuar?')">
                @csrf
                <button class="btn btn-primary" type="submit">Aplicar {{ count($preview['casos']) }} {{ count($preview['casos']) === 1 ? 'convalidació' : 'convalidacions' }}</button>
            </form>
        @else
            <button class="btn btn-primary" type="button" disabled>Aplicar convalidacions</button>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead><tr><th>Destí</th><th>Origen i condicions</th><th>Resultat / normativa</th><th>Estat</th><th>Casos aplicables</th></tr></thead>
            <tbody>
            @forelse ($preview['regles'] as $item)
                @php($rule = $item['regla'])
                <tr>
                    <td><strong>{{ $rule['target']['code'] ?? '—' }} — {{ $rule['target']['name'] ?? 'Destí sense nom' }}</strong><div class="small text-muted">{{ $rule['target']['level'] ?? 'Nivell no indicat' }}</div></td>
                    <td>
                        <div>{{ $rule['source']['name'] ?? ($rule['source']['type'] ?? 'Origen') }}</div>
                        @if (!empty($rule['source']['code']))<div class="small">Codi: {{ $rule['source']['code'] }}</div>@endif
                        @if (!empty($rule['source']['conditions']))<div class="small text-muted">{{ json_encode($rule['source']['conditions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</div>@endif
                        @if (!empty($rule['comment']))<div class="small text-info">{{ $rule['comment'] }}</div>@endif
                        @foreach ($item['correspondencies_angles'] as $correspondencia)
                            <div class="small text-success mt-1">
                                Cicle d’anglés validat: <code>{{ $correspondencia['codi_cicle_angles'] }}</code> — {{ $correspondencia['nom_cicle_angles_val'] }} / {{ $correspondencia['nom_cicle_angles_cas'] }}
                                · cicle contenidor <code>{{ $correspondencia['codi_cicle_contenidor'] }}</code> — {{ $correspondencia['nom_cicle_contenidor_val'] }} / {{ $correspondencia['nom_cicle_contenidor_cas'] }}
                                · {{ $correspondencia['es_grau_superior'] ? 'GS' : 'no GS' }}
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @if (is_array($rule['result'] ?? null))
                            <div>{{ $rule['result']['status'] ?? '—' }} · {{ $rule['result']['grade']['mode'] ?? 'nota no definida' }}</div>
                        @else
                            <div>Sense resultat definit</div>
                        @endif
                        <div class="small text-muted">{{ collect($rule['legal_basis'] ?? [])->pluck('reference')->filter()->implode(', ') ?: 'Sense base normativa' }}</div>
                    </td>
                    <td>
                        @if (!$item['habilitada'])<span class="badge bg-secondary">Deshabilitada</span>
                        @elseif ($item['motiu'])<span class="badge bg-warning text-dark">Pendent</span>
                        @else<span class="badge bg-success">Habilitada</span>@endif
                        @if ($item['motiu'])<div class="small text-muted mt-1">{{ $item['motiu'] }}</div>@endif
                    </td>
                    <td>{{ $item['casos'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No hi ha regles en el catàleg actiu.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
