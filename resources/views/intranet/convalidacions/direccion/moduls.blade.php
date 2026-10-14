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
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#pdf-convalidacions-modal">Descarregar PDF</button>
        </div>
    </div>

    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if (session('warning'))<div class="alert alert-warning" role="status">{{ session('warning') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif

    <form method="GET" action="{{ route('convalidacions.direction.modules.index') }}" class="row">
        <div class="col-md-4 col-sm-6">
            <div class="form-group">
                <label for="filter-estat">Estat</label>
                <select id="filter-estat" name="estat" class="form-control">
                    <option value="">Tots els estats</option>
                    @foreach ($estats as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['estat'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="form-group">
                <div style="padding-top: 25px">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a class="btn btn-default" href="{{ route('convalidacions.direction.modules.index') }}">Netejar</a>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="pdf-convalidacions-modal" tabindex="-1" aria-labelledby="pdf-convalidacions-titol" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('convalidacions.direction.modules.pdf.post') }}" target="_blank" rel="noopener">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Tancar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="pdf-convalidacions-titol">Descarregar PDF</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pdf-estat">Estat de les convalidacions</label>
                            <select id="pdf-estat" name="estat" class="form-control">
                                <option value="">Tots els estats</option>
                                @foreach ($estats as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['estat'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="help-block">Si no selecciones cap estat, el PDF inclourà totes les convalidacions.</p>
                        </div>
                        <div class="form-group" id="pdf-marcar-itaca-contenidor" hidden>
                            <div class="checkbox">
                                <label for="pdf-marcar-itaca">
                                    <input id="pdf-marcar-itaca" type="checkbox" name="marcar_itaca" value="1">
                                    En generar l’informe, marcaré ara aquestes peticions com a «Acceptada (Convalidada)» en la intranet i em faig responsable de registrar-les en ITACA.
                                </label>
                            </div>
                            <p class="help-block">El canvi d’estat serà immediat en la intranet; no es registra res automàticament en ITACA. Només canviaran les peticions que continuen acceptades en este informe.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel·lar</button>
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Generar PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mb-2">{{ $peticions->count() }} {{ $peticions->count() === 1 ? 'mòdul' : 'mòduls' }}</div>
        <div class="table-responsive"><table class="table table-striped align-middle">
            <thead><tr><th scope="col">Alumne</th><th scope="col">Mòdul que vol convalidar</th><th scope="col">Origen i mòdul aportat</th><th scope="col">Estat</th><th scope="col">Sol·licitud</th><th scope="col">Data</th></tr></thead>
            <tbody>
                @forelse ($peticions as $peticio)
                    <tr>
                        <td>{{ $peticio->sollicitud?->alumno?->fullName ?? $peticio->sollicitud?->alumno_id }}<br><small class="text-muted">NIA {{ $peticio->sollicitud?->alumno_id }}</small></td>
                        <td><strong>{{ $peticio->moduloDestino?->vliteral ?? $peticio->modulo_destino_id }}</strong><br><small class="text-muted">{{ $peticio->modulo_destino_id }}</small></td>
                        <td>{{ \Intranet\Entities\Convalidacio::origenLabel($peticio->origen) }}<br>@if ($peticio->modulo_origen_codigo || $peticio->modulo_origen_nombre)<small>{{ $peticio->modulo_origen_codigo }} — {{ $peticio->modulo_origen_nombre }}</small>@endif</td>
                        <td>
                            <span class="badge bg-secondary">{{ $estats[$peticio->estat] ?? $peticio->estat }}</span>
                            @if (filled($peticio->regla_automatica_id))
                                <span role="img" aria-label="Resolució automàtica" title="Resolució automàtica">🤖</span>
                            @endif
                            @if (in_array($peticio->estat, [\Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA, \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA], true) && $peticio->resultat_resolucio)
                                <br><small>Resultat {{ $peticio->resultat_resolucio }}
                                    @if ($peticio->mode_nota_resolucio === 'preserve' && $peticio->nota_resultat_resolucio !== null)
                                        · Nota {{ number_format((float) $peticio->nota_resultat_resolucio, 0, ',', '') }}
                                    @elseif ($peticio->mode_nota_resolucio === 'preserve')
                                        · Nota no disponible
                                    @elseif ($peticio->mode_nota_resolucio === 'none')
                                        · Sense nota
                                    @endif
                                </small>
                            @endif
                        </td>
                        <td>#{{ $peticio->sollicitud_convalidacio_id }}</td>
                        <td>{{ optional($peticio->sollicitud?->submitted_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No hi ha peticions amb estos filtres.</td></tr>
                @endforelse
            </tbody>
        </table></div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const estat = document.getElementById('pdf-estat');
        const opcioItaca = document.getElementById('pdf-marcar-itaca-contenidor');
        const actualitzarOpcioItaca = function () {
            opcioItaca.hidden = estat.value !== @json(\Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA);
            document.getElementById('pdf-marcar-itaca').disabled = opcioItaca.hidden;
        };
        estat.addEventListener('change', actualitzarOpcioItaca);
        actualitzarOpcioItaca();
    });
</script>
@endsection
