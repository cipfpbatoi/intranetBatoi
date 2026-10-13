<p>Hola {{ $nomAlumne }},</p>

<p>Direcció ha actualitzat la petició de convalidació del mòdul <strong>{{ $modul }}</strong>.</p>

<p><strong>Nou estat:</strong> {{ $estat }}</p>

@if (filled($observacions))
    <p><strong>Comentari de Direcció:</strong> {{ $observacions }}</p>
@endif

<p>Pots consultar les teues sol·licituds de convalidació des de la intranet.</p>
