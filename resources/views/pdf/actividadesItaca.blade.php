@extends('layouts.pdf')
@section('content')
    @include('pdf.partials.cabecera')
    <h2>{{ __('models.Actividad.itaca_list') }}</h2>
    <table class="table table-bordered" style="font-size: 8pt; width: 100%;">
        <thead>
            <tr>
                <th>N</th>
                <th>Activitat</th>
                <th>Descripció</th>
                <th>Objectius</th>
                <th>Des de</th>
                <th>Fins</th>
                <th>Ubicació / transport</th>
                <th>Comentaris</th>
                <th>Professorat</th>
                <th>Grups</th>
                <th>{{ __('models.Actividad.itaca_category') }}</th>
                <th>{{ __('models.Actividad.itaca_coordinator') }}</th>
                <th>{{ __('models.Actividad.authorization_print_date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todos as $elemento)
                @php
                    $coordinador = $elemento->profesores->first(fn ($profesor) => (bool) $profesor->pivot->coordinador);
                @endphp
                <tr>
                    <td>{{ $elemento->id }}</td>
                    <td>{{ $elemento->name }}</td>
                    <td>{{ $elemento->descripcion }}</td>
                    <td>{{ $elemento->objetivos }}</td>
                    <td>{{ $elemento->desde }}</td>
                    <td>{{ $elemento->hasta }}</td>
                    <td>{{ $elemento->getUbicacioActivitatOptions()[$elemento->ubicacio_activitat] }}</td>
                    <td>{{ $elemento->comentarios }}</td>
                    <td>
                        @foreach ($elemento->profesores as $profesor)
                            {{ $profesor->fullName }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($elemento->grupos as $grupo)
                            {{ $grupo->nombre }}<br>
                        @endforeach
                    </td>
                    <td>{{ $elemento->tipoActividad->vliteral ?? __('models.Actividad.unassigned') }}</td>
                    <td>{{ $coordinador->fullName ?? __('models.Actividad.unassigned') }}</td>
                    <td>{{ $elemento->fecha_impresion?->format('d-m-Y') ?? __('models.Actividad.no_print_date') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
