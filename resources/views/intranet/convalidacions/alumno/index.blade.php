@extends('layouts.intranet')

@section('titulo', 'Convalidacions')

@section('content')
<div class="container">
    <h1>Convalidacions</h1>
    
    <div class="card mt-3">
        <div class="card-body">
            <a href="{{ route('convalidacions.create') }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Nova sol·licitud
            </a>

            @if ($sollicituds->isEmpty())
                <div class="alert alert-info">
                    No tens cap sol·licitud de convalidació.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Estat</th>
                                <th>Mòduls</th>
                                <th>Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sollicituds as $sollicitud)
                                <tr>
                                    <td>{{ $sollicitud->data_sol·licitud->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if ($sollicitud->estat == 'pendent') bg-warning
                                            @elseif ($sollicitud->estat == 'aprovat') bg-success
                                            @elseif ($sollicitud->estat == 'rebutjat') bg-danger
                                            @elseif ($sollicitud->estat == 'documents_requerits') bg-info
                                            @endif">
                                            {{ $sollicitud->estat }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $sollicitud->convalidacions->count() }} mòduls
                                    </td>
                                    <td>
                                        <a href="{{ route('convalidacions.show', $sollicitud->id) }}" class="btn btn-sm btn-info">
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
    </div>
</div>
@endsection
