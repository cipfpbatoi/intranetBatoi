@php
    $opcionsPromocio = config('auxiliares.promociona', []);
@endphp
<div class="container">
    <br/>
    <strong>Promoció de l'alumnat</strong>
    <ul style='list-style:none'>
        @foreach ($datosInforme->alumnos()->orderBy('apellido1')->orderBy('apellido2')->get() as $alumno)
            @php
                $capacitats = (int) data_get($alumno, 'pivot.capacitats', 0);
                $decisio = match ($capacitats) {
                    1 => 'SI',
                    3 => 'NO',
                    default => 'Sense determinar',
                };
                $textPromocio = $opcionsPromocio[$capacitats] ?? null;
            @endphp
            <li>
                <strong>{{ $alumno->nameFull }} - {{ $decisio }}</strong>
                @if ($textPromocio)
                    - {{ $textPromocio }}
                @endif
            </li>
        @endforeach
    </ul>
</div>
