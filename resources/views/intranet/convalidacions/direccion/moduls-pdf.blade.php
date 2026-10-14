<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Convalidacions per mòdul</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #20252a; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #9aa0a6; padding: 6px; vertical-align: top; text-align: left; }
        th { background: #e9edf1; }
        .muted { color: #555; font-size: 9px; }
        .responsable { margin: 0 0 12px; padding: 8px; border: 1px solid #9aa0a6; }
    </style>
</head>
<body>
    <h1>Convalidacions per mòdul</h1>
    <div class="meta">Generat el {{ $generat->format('d/m/Y H:i') }} · {{ $peticions->count() }} peticions</div>
    @if ($registreItacaConfirmat)
        <div class="responsable"><strong>Marcades com a «Acceptada (Convalidada)» en la intranet per:</strong> {{ $generatPer->fullName ?? $generatPer->dni }}. Esta persona és responsable de registrar-les en ITACA.</div>
        @if ($errorsCorreu > 0)
            <div class="responsable">Avís: no s’ha pogut enviar la notificació a {{ $errorsCorreu }} {{ $errorsCorreu === 1 ? 'alumne' : 'alumnes' }}.</div>
        @endif
    @elseif ($estatInforme === \Intranet\Entities\Convalidacio::ESTAT_ACCEPTADA)
        <div class="responsable"><strong>Persona responsable de registrar aquest informe en ITACA:</strong> {{ $generatPer->fullName ?? $generatPer->dni }}</div>
    @else
        <div class="meta">Informe generat per {{ $generatPer->fullName ?? $generatPer->dni }}</div>
    @endif
    <table>
        <thead><tr><th>Alumne i NIA</th><th>Mòdul sol·licitat</th><th>Origen i mòdul aportat</th><th>Resultat i nota</th><th>Estat</th><th>Sol·licitud</th><th>Dates</th></tr></thead>
        <tbody>
            @forelse ($peticions as $peticio)
                <tr>
                    <td>{{ $peticio->sollicitud?->alumno?->fullName ?? $peticio->sollicitud?->alumno_id }}<br><span class="muted">NIA {{ $peticio->sollicitud?->alumno_id }}</span></td>
                    <td><strong>{{ $peticio->moduloDestino?->vliteral ?? $peticio->modulo_destino_id }}</strong><br><span class="muted">{{ $peticio->modulo_destino_id }}</span></td>
                    <td>{{ \Intranet\Entities\Convalidacio::origenLabel($peticio->origen) }}<br><span class="muted">{{ $peticio->modulo_origen_codigo }} {{ $peticio->modulo_origen_nombre }}</span></td>
                    <td>{{ $peticio->resultat_resolucio ?: '—' }}@if ($peticio->mode_nota_resolucio === 'preserve' && $peticio->nota_resultat_resolucio !== null)<br><span class="muted">Nota {{ rtrim(rtrim(number_format((float) $peticio->nota_resultat_resolucio, 2, ',', ''), '0'), ',') }}</span>@elseif ($peticio->mode_nota_resolucio === 'preserve')<br><span class="muted">Nota no disponible</span>@elseif ($peticio->mode_nota_resolucio === 'none')<br><span class="muted">Sense nota</span>@endif</td>
                    <td>{{ \Intranet\Entities\Convalidacio::estatOptions()[$peticio->estat] ?? $peticio->estat }}</td>
                    <td>#{{ $peticio->sollicitud_convalidacio_id }}</td>
                    <td><span class="muted">Sol·licitud:</span> {{ optional($peticio->sollicitud?->submitted_at)->format('d/m/Y H:i') }}<br><span class="muted">Estat:</span> {{ $peticio->estat_canviat_at?->format('d/m/Y H:i') ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No hi ha peticions amb els filtres seleccionats.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
