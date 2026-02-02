@extends('layouts.intranet')

@section('content')
    <h2>Propostes de canvi d'horari</h2>

    <div class="mb-3">
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Pendiente') btn-primary @endif" href="/direccion/horario/propuestas?estado=Pendiente">Pendents</a>
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Aceptado') btn-primary @endif" href="/direccion/horario/propuestas?estado=Aceptado">Acceptades</a>
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Todos') btn-primary @endif" href="/direccion/horario/propuestas?estado=Todos">Totes</a>
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
                    <td>{{ $proposta['obs'] }}</td>
                    <td>
                        <a class="btn btn-default" href="/profesor/{{ $proposta['dni'] }}/horario-cambiar?proposta={{ $proposta['id'] }}">Veure</a>
                        @if (($proposta['estado'] ?? 'Pendiente') === 'Pendiente')
                            <a class="btn btn-primary" href="/direccion/horario/propuesta/{{ $proposta['dni'] }}/{{ $proposta['id'] }}/aceptar" onclick="return confirm('Acceptar aquesta proposta?')">Acceptar</a>
                        @endif
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
