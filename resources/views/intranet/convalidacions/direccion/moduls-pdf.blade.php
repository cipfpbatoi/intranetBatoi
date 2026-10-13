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
    </style>
</head>
<body>
    <h1>Convalidacions per mòdul</h1>
    <div class="meta">Generat el {{ $generat->format('d/m/Y H:i') }} · {{ $peticions->count() }} peticions</div>
    <table>
        <thead><tr><th>Alumne i NIA</th><th>Mòdul sol·licitat</th><th>Origen i mòdul aportat</th><th>Estat</th><th>Sol·licitud</th><th>Data</th></tr></thead>
        <tbody>
            @forelse ($peticions as $peticio)
                <tr>
                    <td>{{ $peticio->sollicitud?->alumno?->fullName ?? $peticio->sollicitud?->alumno_id }}<br><span class="muted">NIA {{ $peticio->sollicitud?->alumno_id }}</span></td>
                    <td><strong>{{ $peticio->moduloDestino?->vliteral ?? $peticio->modulo_destino_id }}</strong><br><span class="muted">{{ $peticio->modulo_destino_id }}</span></td>
                    <td>{{ \Intranet\Entities\Convalidacio::origenLabel($peticio->origen) }}<br><span class="muted">{{ $peticio->modulo_origen_codigo }} {{ $peticio->modulo_origen_nombre }}</span></td>
                    <td>{{ \Intranet\Entities\Convalidacio::estatOptions()[$peticio->estat] ?? $peticio->estat }}</td>
                    <td>#{{ $peticio->sollicitud_convalidacio_id }}</td>
                    <td>{{ optional($peticio->sollicitud?->submitted_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No hi ha peticions amb els filtres seleccionats.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
