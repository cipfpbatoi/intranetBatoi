@extends('layouts.intranet')

@section('titulo', 'Detall de la convalidació')

@section('content')
<div class="container">
    <h1>Detall de la sol·licitud</h1>
    <p class="text-muted">Data: {{ $sollicitud->data_sol·licitud->format('d/m/Y H:i') }}</p>
    
    <div class="card mt-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Informació general</h5>
                    <p><strong>Estat:</strong>
                        <span class="badge 
                            @if ($sollicitud->estat == 'pendent') bg-warning
                            @elseif ($sollicitud->estat == 'aprovat') bg-success
                            @elseif ($sollicitud->estat == 'rebutjat') bg-danger
                            @elseif ($sollicitud->estat == 'documents_requerits') bg-info
                            @endif">
                            {{ $sollicitud->estat }}
                        </span>
                    </p>
                    @if ($sollicitud->observacions)
                        <div class="alert alert-info">
                            <strong>Observacions:</strong> {{ $sollicitud->observacions }}
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    @if ($sollicitud->estat == 'pendent')
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i>
                            Este formulari està pendent de resolució.
                        </div>
                    @elseif ($sollicitud->estat == 'aprovat')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Este formulari ha estat aprovat.
                        </div>
                    @elseif ($sollicitud->estat == 'rebutjat')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i>
                            Este formulari ha estat rebutjat.
                        </div>
                    @elseif ($sollicitud->estat == 'documents_requerits')
                        <div class="alert alert-info">
                            <i class="fas fa-file-contract"></i>
                            Es requereixen documents addicionals.
                        </div>
                    @endif
                </div>
            </div>

            <h5>Mòduls convalidats</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mòdul</th>
                            <th>Tipus</th>
                            <th>Estat</th>
                            <th>Detalls</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sollicitud->convalidacions as $convalidacio)
                            <tr>
                                <td>
                                    @if ($convalidacio->modulo)
                                        {{ $convalidacio->modulo->literal }}
                                    @else
                                        <em>N/A</em>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $convalidacio->tipus_convalidacio }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if ($convalidacio->estat == 'pendent') bg-warning
                                        @elseif ($convalidacio->estat == 'aprovat') bg-success
                                        @elseif ($convalidacio->estat == 'rebutjat') bg-danger
                                        @endif">
                                        {{ $convalidacio->estat }}
                                    </span>
                                </td>
                                <td>
                                    @if ($convalidacio->tipus_convalidacio == 'mateix_centre' && $convalidacio->cicleFormatiuCursat)
                                        <small>Cicle: {{ $convalidacio->cicleFormatiuCursat->cicle?->literal ?? 'Cicle ' . $convalidacio->cicleFormatiuCursat->any_curs }}</small>
                                    @elseif ($convalidacio->certificat_path)
                                        <a href="{{ route('convalidacions.download', $convalidacio->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-download"></i> Descarregar certificat
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('convalidacions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Tornar
        </a>
    </div>
</div>
@endsection
