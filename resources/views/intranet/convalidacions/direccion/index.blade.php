@extends('layouts.intranet')

@section('titulo', 'Gestió de convalidacions')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="mb-0">Gestió de convalidacions</h1>
        <a class="btn btn-outline-primary" href="{{ route('convalidacions.direction.xml.index') }}">
            <i class="fa fa-list-alt" aria-hidden="true"></i> Gestionar avaluacions d'ITACA
        </a>
    </div>
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4"><select name="estat" class="form-select"><option value="">Tots els estats</option>@foreach ($estats as $value => $label)<option value="{{ $value }}" @selected(($filters['estat'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4"><select name="origen" class="form-select"><option value="">Tots els orígens</option>@foreach ($origens as $value => $label)<option value="{{ $value }}" @selected(($filters['origen'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4"><button class="btn btn-primary">Filtrar</button> <a class="btn btn-secondary" href="{{ route('convalidacions.direction.index') }}">Netejar</a></div>
    </form>
    <div class="table-responsive"><table class="table table-striped">
        <thead><tr><th>Data</th><th>Alumne</th><th>Peticions</th><th></th></tr></thead>
        <tbody>@forelse ($sollicituds as $sollicitud)<tr><td>{{ $sollicitud->submitted_at->format('d/m/Y H:i') }}</td><td>{{ $sollicitud->alumno?->fullName ?? $sollicitud->alumno_id }}</td><td>{{ $sollicitud->convalidacions->count() }}</td><td><a href="{{ route('convalidacions.direction.show', $sollicitud) }}">Revisar</a></td></tr>@empty<tr><td colspan="4">No hi ha sol·licituds amb estos filtres.</td></tr>@endforelse</tbody>
    </table></div>
</div>
@endsection
