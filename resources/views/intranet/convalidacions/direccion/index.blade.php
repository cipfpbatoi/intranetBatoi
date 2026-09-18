@extends('intranet.index')

@section('panel', 'Convalidacions')

@section('content')
<div class="container">
    <h1>Convalidacions pendents</h1>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Gestiona les sol·licituds de convalidació dels alumnes.
    </div>

    @if ($sollicituds->isEmpty())
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            No hi ha cap sol·licitud pendent de resolució.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="convalidacions-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Alumne</th>
                        <th>Mòduls</th>
                        <th>Tipus</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sollicituds as $sollicitud)
                        <tr>
                            <td>{{ $sollicitud->data_sol·licitud->format('d/m/Y H:i') }}</td>
                            <td>{{ $sollicitud->alumno->fullName ?? 'Desconegut' }}</td>
                            <td>{{ $sollicitud->convalidacions->count() }} mòduls</td>
                            <td>
                                @php
                                    $tipusUnics = $sollicitud->convalidacions->pluck('tipus_convalidacio')->unique();
                                @endphp
                                @foreach ($tipusUnics as $tipus)
                                    <span class="badge bg-primary me-1">{{ $tipus }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('convalidacions.direction.show', $sollicitud->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detall
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#convalidacions-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ca.json'
        },
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>
@endpush
