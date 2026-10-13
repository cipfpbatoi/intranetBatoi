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
    <div class="table-responsive"><table class="table table-striped">
        <thead><tr><th>Referència</th><th>Data</th><th>Alumne</th><th>Mòduls</th><th>Automatització</th><th></th></tr></thead>
        <tbody>
            @forelse ($sollicituds as $sollicitud)
                @php($automatitzacio = $automatitzacions[$sollicitud->id] ?? ['pendents' => 0, 'elegibles' => 0])
                <tr>
                    <td>#{{ $sollicitud->id }}</td>
                    <td>{{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sollicitud->alumno?->fullName ?? $sollicitud->alumno_id }}</td>
                    <td>{{ $sollicitud->convalidacions->count() }}</td>
                    <td>
                        @if ($automatitzacio['pendents'] === 0)
                            <span class="label label-default">Sense peticions pendents</span>
                        @elseif ($automatitzacio['elegibles'] === 0)
                            <span class="label label-default">Revisió manual</span>
                        @elseif ($automatitzacio['elegibles'] === $automatitzacio['pendents'])
                            <span class="label label-success">Automàtica · {{ $automatitzacio['elegibles'] }}/{{ $automatitzacio['pendents'] }}</span>
                        @else
                            <span class="label label-warning">Parcialment automàtica · {{ $automatitzacio['elegibles'] }}/{{ $automatitzacio['pendents'] }}</span>
                        @endif
                    </td>
                    <td><a href="{{ route('convalidacions.direction.show', $sollicitud) }}">Revisar</a></td>
                </tr>
            @empty
                <tr><td colspan="6">No hi ha sol·licituds.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endsection
