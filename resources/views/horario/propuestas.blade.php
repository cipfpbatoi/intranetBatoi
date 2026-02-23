@extends('layouts.intranet')

@section('content')
    <h2>Propostes de canvi d'horari</h2>

    <div class="mb-3">
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Pendiente') btn-primary @endif" href="/direccion/horario/propuestas?estado=Pendiente">Pendents</a>
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Aceptado') btn-primary @endif" href="/direccion/horario/propuestas?estado=Aceptado">Acceptades</a>
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Rebutjat') btn-primary @endif" href="/direccion/horario/propuestas?estado=Rebutjat">Rebutjades</a>
        <a class="btn btn-default @if(($estado ?? 'Pendiente') === 'Todos') btn-primary @endif" href="/direccion/horario/propuestas?estado=Todos">Totes</a>
    </div>

    @php
        $isDireccion = authUser() && esRol(authUser()->rol, config('roles.rol.direccion'));
    @endphp

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
                    <td>
                        {{ $proposta['estado'] }}
                        @if (($proposta['estado'] ?? '') === 'Guardado')
                            <div class="text-muted">Ja aplicat, no es pot acceptar</div>
                        @endif
                    </td>
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
                        <a class="btn btn-default" href="/profesor/{{ $proposta['dni'] }}/horario-cambiar?proposta={{ $proposta['id'] }}" title="Veure" aria-label="Veure">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                        @php
                            $estadoProposta = $proposta['estado'] ?? 'Pendiente';
                            $puedeAceptar = !in_array($estadoProposta, ['Aceptado', 'Guardado'], true);
                        @endphp
                        @if ($isDireccion && $puedeAceptar)
                            <a class="btn btn-primary" href="/direccion/horario/propuesta/{{ $proposta['dni'] }}/{{ $proposta['id'] }}/aceptar" onclick="return confirm('Acceptar aquesta proposta?')" title="Acceptar" aria-label="Acceptar">
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-danger" href="/direccion/horario/propuesta/{{ $proposta['dni'] }}/{{ $proposta['id'] }}/rebutjar" data-dni="{{ $proposta['dni'] }}" data-id="{{ $proposta['id'] }}" onclick="return rebutjarProposta(this.dataset.dni, this.dataset.id)" title="Rebutjar" aria-label="Rebutjar">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </a>
                        @endif
                        <a class="btn btn-danger" href="/direccion/horario/propuesta/{{ $proposta['dni'] }}/{{ $proposta['id'] }}/esborrar" onclick="return confirm('Esborrar aquesta proposta?')" title="Esborrar" aria-label="Esborrar">
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
            var url = '/direccion/horario/propuesta/' + dni + '/' + id + '/rebutjar?motiu=' + encodeURIComponent(motiu);
            window.location.href = url;
            return false;
        }
    </script>
@endpush
