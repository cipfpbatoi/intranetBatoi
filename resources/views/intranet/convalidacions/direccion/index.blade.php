@extends('layouts.intranet')

@section('titulo', 'Sol·licituds de convalidació')

@section('content')
<div class="container">
    <nav aria-label="Vistes de convalidacions">
        <ul class="nav nav-tabs bar_tabs">
            <li class="active"><a href="{{ route('convalidacions.direction.index') }}" aria-current="page">Sol·licituds</a></li>
            <li><a href="{{ route('convalidacions.direction.modules.index') }}">Convalidacions per mòdul</a></li>
            <li><a href="{{ route('convalidacions.direction.configuracio') }}">Configuració de convalidacions</a></li>
        </ul>
    </nav>
    <p>Cada fila agrupa una sol·licitud de l’alumne i tots els mòduls que ha demanat.</p>
    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if (session('warning'))<div class="alert alert-warning" role="status">{{ session('warning') }}</div>@endif
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
    <details class="panel panel-default">
        <summary class="panel-heading" style="cursor:pointer">
            <strong>Entorn de proves</strong>
            @if ($accessBlocked)
                <span class="label label-warning pull-right">Accés restringit</span>
            @else
                <span class="label label-success pull-right">Accés obert</span>
            @endif
        </summary>
        <div class="panel-body">
            <div>
                <p id="convalidacions-access-help">En restringir-lo, l’alumnat necessitarà la contrasenya de proves per accedir-hi.</p>
                <p><span class="text-muted">Contrasenya de proves:</span> <code>{{ $accessPassword }}</code></p>
                @if ($accessBlocked)
                    <div class="alert alert-warning" role="status"><strong>Les convalidacions estan restringides per a l’alumnat.</strong></div>
                @endif
            </div>
            <form method="POST" action="{{ route('convalidacions.direction.access') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="blocked" value="{{ $accessBlocked ? 1 : 0 }}">
                <div class="checkbox">
                    <label for="convalidacions-access-switch">
                    <input type="checkbox" role="switch" id="convalidacions-access-switch"
                        aria-describedby="convalidacions-access-help" @checked($accessBlocked)
                        onchange="this.form.querySelector('input[name=blocked]').value = this.checked ? '1' : '0'; this.form.requestSubmit()">
                    {{ $accessBlocked ? 'Bloquejat' : 'Obert' }}
                    </label>
                </div>
            </form>
        </div>
    </details>
    <nav aria-label="Pestanyes de sol·licituds">
        <ul class="nav nav-tabs bar_tabs">
            <li @class(['active' => $pestanya === 'seguiment'])>
                <a href="{{ route('convalidacions.direction.index', ['pestanya' => 'seguiment', 'pendent_direccio' => $filters['pendent_direccio'] ? 1 : null]) }}"
                   @if ($pestanya === 'seguiment') aria-current="page" @endif>
                    En seguiment <span class="badge">{{ $sollicitudsEnSeguiment }}</span>
                </a>
            </li>
            <li @class(['active' => $pestanya === 'resoltes'])>
                <a href="{{ route('convalidacions.direction.index', ['pestanya' => 'resoltes']) }}"
                   @if ($pestanya === 'resoltes') aria-current="page" @endif>
                    Resoltes
                </a>
            </li>
        </ul>
    </nav>
    @if ($pestanya === 'seguiment')
    <section class="x_panel" aria-labelledby="automatic-action-heading">
        <div class="x_title">
            <h2 id="automatic-action-heading">Resolució automàtica</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div>
                <p class="text-muted">Només s’aplicarà a les peticions que complisquen les regles actuals; la resta continuarà pendent de revisió manual.</p>
            </div>
            @if ($peticionsAutomatiquesElegibles > 0)
                <form method="POST" action="{{ route('convalidacions.direction.rules.apply') }}"
                    data-confirm="S’aplicaran {{ $peticionsAutomatiquesElegibles }} peticions elegibles del catàleg actiu. Les peticions no elegibles continuaran pendents per a revisió manual. No s’actualitzarà ITACA. Vols continuar?"
                    onsubmit="return confirm(this.dataset.confirm)">
                    @csrf
                    <input type="hidden" name="return_to" value="index">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="fa fa-play" aria-hidden="true"></i> Aplicar ara {{ $peticionsAutomatiquesElegibles }} {{ $peticionsAutomatiquesElegibles === 1 ? 'convalidació automàtica' : 'convalidacions automàtiques' }}
                    </button>
                </form>
            @else
                <p class="alert alert-info" role="status">Ara mateix no hi ha convalidacions automàtiques elegibles.</p>
            @endif
        </div>
    </section>
    @endif
    @if ($pestanya === 'seguiment')
        <form method="GET" action="{{ route('convalidacions.direction.index') }}" class="form-inline mb-3">
            <input type="hidden" name="pestanya" value="seguiment">
            <label for="filter-pendent-direccio" class="mr-2">Mostrar</label>
            <select id="filter-pendent-direccio" name="pendent_direccio" class="form-control mr-2" onchange="this.form.submit()">
                <option value="0" @selected(!$filters['pendent_direccio'])>Totes les sol·licituds en seguiment</option>
                <option value="1" @selected($filters['pendent_direccio'])>Només amb acció pendent de Direcció</option>
            </select>
        </form>
    @else
        <p class="text-muted">Sol·licituds en què tots els mòduls estan acceptats, denegats o convalidats.</p>
    @endif
    <div class="table-responsive"><table class="table table-striped">
        <thead><tr><th>Referència</th><th>Data</th><th>Alumne</th><th>Mòduls</th><th>Acció pendent de Direcció</th>@if ($pestanya === 'seguiment')<th>Automatització</th>@endif<th></th></tr></thead>
        <tbody>
            @forelse ($sollicituds as $sollicitud)
                @php($automatitzacio = $automatitzacions[$sollicitud->id] ?? ['pendents' => 0, 'elegibles' => 0])
                @php($enviades = $sollicitud->convalidacions->where('estat', \Intranet\Entities\Convalidacio::ESTAT_ENVIADA)->count())
                @php($enProces = $sollicitud->convalidacions->where('estat', \Intranet\Entities\Convalidacio::ESTAT_EN_PROCES)->count())
                @php($accionsPendents = $enviades + $enProces)
                @php($peticionsPendentsAuto = $automatitzacio['pendents'])
                @php($peticionsAuto = $automatitzacio['elegibles'])
                @php($peticionsManuals = $peticionsPendentsAuto - $peticionsAuto)
                <tr>
                    <td>#{{ $sollicitud->id }}</td>
                    <td>{{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sollicitud->alumno?->fullName ?? $sollicitud->alumno_id }}</td>
                    <td>{{ $sollicitud->convalidacions->count() }}</td>
                    <td>
                        @if ($accionsPendents > 0)
                            @php($desglossament = trim(($enviades > 0 ? $enviades . ' Enviada' : '') . ($enviades > 0 && $enProces > 0 ? ', ' : '') . ($enProces > 0 ? $enProces . ' En procés' : '')))
                            <span class="label label-primary" role="img" title="Acció pendent: {{ $desglossament }}."
                                  aria-label="{{ $accionsPendents }} {{ $accionsPendents === 1 ? 'mòdul' : 'mòduls' }} amb acció pendent de Direcció: {{ $desglossament }}.">
                                {{ $accionsPendents }} {{ $accionsPendents === 1 ? 'mòdul' : 'mòduls' }}
                            </span>
                        @endif
                    </td>
                    @if ($pestanya === 'seguiment')
                        <td>
                            @if ($peticionsPendentsAuto > 0 && $peticionsAuto === 0)
                                <span role="img"
                                      aria-label="Revisió manual: no hi ha cap regla automàtica aplicable a {{ $peticionsPendentsAuto }} {{ $peticionsPendentsAuto === 1 ? 'petició pendent' : 'peticions pendents' }}."
                                      title="Revisió manual: no hi ha cap regla automàtica aplicable a {{ $peticionsPendentsAuto }} {{ $peticionsPendentsAuto === 1 ? 'petició pendent' : 'peticions pendents' }}.">✍️</span>
                            @elseif ($peticionsAuto === $peticionsPendentsAuto)
                                @if ($peticionsPendentsAuto > 0)
                                    <span role="img"
                                          aria-label="Resolució automàtica: {{ $peticionsAuto }} {{ $peticionsAuto === 1 ? 'petició pendent elegible' : 'peticions pendents elegibles' }} per a aplicació automàtica."
                                          title="Resolució automàtica: {{ $peticionsAuto }} {{ $peticionsAuto === 1 ? 'petició pendent elegible' : 'peticions pendents elegibles' }} per a aplicació automàtica.">🤖</span>
                                @endif
                            @elseif ($peticionsPendentsAuto > 0)
                                <span role="img"
                                      aria-label="Resolució mixta: {{ $peticionsAuto }} de {{ $peticionsPendentsAuto }} peticions pendents es poden resoldre automàticament; {{ $peticionsManuals }} {{ $peticionsManuals === 1 ? 'requerix' : 'requerixen' }} revisió manual."
                                      title="Resolució mixta: {{ $peticionsAuto }} de {{ $peticionsPendentsAuto }} peticions pendents es poden resoldre automàticament; {{ $peticionsManuals }} {{ $peticionsManuals === 1 ? 'requerix' : 'requerixen' }} revisió manual.">🤖✍️</span>
                            @endif
                        </td>
                    @endif
                    <td>
                        @if ($enviades > 0)
                            <form method="POST" action="{{ route('convalidacions.direction.review', $sollicitud) }}">
                                @csrf
                                <button class="btn btn-primary btn-sm" type="submit">Revisar</button>
                            </form>
                        @else
                            <a class="btn btn-primary btn-sm" href="{{ route('convalidacions.direction.show', $sollicitud) }}">{{ $pestanya === 'resoltes' ? 'Veure' : 'Revisar' }}</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ $pestanya === 'seguiment' ? 7 : 6 }}">No hi ha sol·licituds.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endsection
