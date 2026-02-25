@extends('layouts.intranet')

@section('content')
    <h2>Propostes de canvi d'horari</h2>

    <div class="mb-3">
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Pendiente') btn-primary @endif" href="{{ route('horario.propuestas', ['estado' => 'Pendiente']) }}">Pendents</a>
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Aceptado') btn-primary @endif" href="{{ route('horario.propuestas', ['estado' => 'Aceptado']) }}">Acceptades</a>
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Rebutjat') btn-primary @endif" href="{{ route('horario.propuestas', ['estado' => 'Rebutjat']) }}">Rebutjades</a>
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Todos') btn-primary @endif" href="{{ route('horario.propuestas', ['estado' => 'Todos']) }}">Totes</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Professor</th>
                <th>Estat</th>
                <th>Dates</th>
                <th>Canvis</th>
                <th>Observacions</th>
                <th>Accions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($propuestas as $proposta)
                <tr>
                    <td>
                        {{ $proposta['profesor']->fullName ?? $proposta['dni'] }}
                        <div class="text-muted">{{ $proposta['dni'] }}</div>
                    </td>
                    <td>{{ $proposta['estado'] }}</td>
                    <td>
                        {{ $proposta['fecha_inicio'] ?: '-' }}
                        <br>
                        {{ $proposta['fecha_fin'] ?: '-' }}
                    </td>
                    <td>{{ count($proposta['cambios']) }}</td>
                    <td>
                        {{ $proposta['obs'] }}
                        @if (!empty($proposta['motiu_rebuig']))
                            <div class="text-danger">Motiu rebuig: {{ $proposta['motiu_rebuig'] }}</div>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-default" href="{{ route('horario.profesor.change', ['profesor' => $proposta['dni'], 'proposta' => $proposta['id']]) }}" title="Veure" aria-label="Veure">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                        @if (($proposta['estado'] ?? 'Pendiente') === 'Pendiente')
                            <a class="btn btn-primary" href="{{ route('horario.propuesta.aceptar', ['dni' => $proposta['dni'], 'id' => $proposta['id']]) }}" onclick="return confirm('Acceptar aquesta proposta?')" title="Acceptar" aria-label="Acceptar">
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-danger" href="{{ route('horario.propuesta.rebutjar', ['dni' => $proposta['dni'], 'id' => $proposta['id']]) }}" onclick="return rebutjarProposta('{{ $proposta['dni'] }}','{{ $proposta['id'] }}')" title="Rebutjar" aria-label="Rebutjar">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </a>
                        @endif
                        <a class="btn btn-danger" href="{{ route('horario.propuesta.esborrar', ['dni' => $proposta['dni'], 'id' => $proposta['id']]) }}" onclick="return confirm('Esborrar aquesta proposta?')" title="Esborrar" aria-label="Esborrar">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hi ha propostes pendents.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

@section('titulo')
    Propostes d'horari
@endsection

@push('scripts')
    <script>
        function rebutjarProposta(dni, id) {
            var motiu = prompt('Motiu del rebuig?');
            if (!motiu) return false;
            var urlTemplate = @json(route('horario.propuesta.rebutjar', ['dni' => '__DNI__', 'id' => '__ID__']));
            var url = urlTemplate
                .replace('__DNI__', encodeURIComponent(dni))
                .replace('__ID__', encodeURIComponent(id))
                + '?motiu=' + encodeURIComponent(motiu);
            window.location.href = url;
            return false;
        }
    </script>
@endpush
