@extends('intranet.index')

@section('panel', 'Convalidacions')

@section('content')
<div class="container">
    <h1>Detall de la sol·licitud</h1>
    <p class="text-muted">Data: {{ $sollicitud->data_sol·licitud->format('d/m/Y H:i') }}</p>
    
    <div class="card mt-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Informació de l'alumne</h5>
                    <p><strong>NIA:</strong> {{ $sollicitud->alumno->nia }}</p>
                    <p><strong>Alumne:</strong> {{ $sollicitud->alumno->fullName ?? 'Desconegut' }}</p>
                    <p><strong>Cicle actual:</strong> 
                        {{ optional($sollicitud->alumno->Grupo->first())->ciclo->literal ?? 'Sense cicle' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Informació de la sol·licitud</h5>
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
                </div>
            </div>

            <h5>Mòduls convalidats</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mòdul</th>
                            <th>Tipus</th>
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
                                    @if ($convalidacio->tipus_convalidacio == 'mateix_centre' && $convalidacio->cicleFormatiuCursat)
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Cicle cursat:</strong> {{ $convalidacio->cicleFormatiuCursat->cicle?->literal ?? 'Cicle ' . $convalidacio->cicleFormatiuCursat->any_curs }}
                                        </div>
                                    @elseif ($convalidacio->certificat_path)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-file-contract"></i>
                                            <strong>Certificat:</strong>
                                            <a href="{{ route('convalidacions.download', $convalidacio->id) }}" class="alert-link">
                                                Descarregar certificat
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5>Resoldre la sol·licitud</h5>
            <form id="resolve-form" method="POST" action="{{ route('convalidacions.direction.resolve', $sollicitud->id) }}">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Estat:</label>
                    <select class="form-select" name="estat" required>
                        <option value="">Triar estat...</option>
                        <option value="aprovat">Aprovar</option>
                        <option value="rebutjat">Rebutjar</option>
                        <option value="documents_requerits">Documents requerits</option>
                    </select>
                </div>

                <div class="mb-3" id="observacions-container" style="display: none;">
                    <label class="form-label">Observacions (opcional):</label>
                    <textarea class="form-control" name="observacions" rows="3" placeholder="Motiu de la resolució..."></textarea>
                </div>

                <button type="submit" class="btn btn-success" id="btn-aprovar">
                    <i class="fas fa-check"></i> Aprovar
                </button>
                <button type="submit" class="btn btn-danger" id="btn-rebutjar">
                    <i class="fas fa-times"></i> Rebutjar
                </button>
                <button type="submit" class="btn btn-info" id="btn-documents">
                    <i class="fas fa-file-contract"></i> Documents requerits
                </button>

                <a href="{{ route('convalidacions.direction.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Tornar
                </a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resolve-form');
    const estatSelect = form.querySelector('select[name="estat"]');
    const observacionsContainer = document.getElementById('observacions-container');
    const btnAprovar = document.getElementById('btn-aprovar');
    const btnRebutjar = document.getElementById('btn-rebutjar');
    const btnDocuments = document.getElementById('btn-documents');

    estatSelect.addEventListener('change', function() {
        if (this.value === 'documents_requerits') {
            observacionsContainer.style.display = 'block';
            observacionsContainer.querySelector('textarea').required = true;
            btnAprovar.disabled = true;
            btnRebutjar.disabled = true;
            btnDocuments.disabled = false;
            btnDocuments.classList.remove('d-none');
        } else {
            observacionsContainer.style.display = 'none';
            observacionsContainer.querySelector('textarea').required = false;
            btnAprovar.disabled = this.value !== 'aprovat';
            btnRebutjar.disabled = this.value !== 'rebutjat';
            
            if (this.value === '') {
                btnAprovar.disabled = true;
                btnRebutjar.disabled = true;
                btnDocuments.disabled = true;
            }
        }
    });

    btnAprovar.addEventListener('click', function(e) {
        estatSelect.value = 'aprovat';
    });

    btnRebutjar.addEventListener('click', function(e) {
        estatSelect.value = 'rebutjat';
        observacionsContainer.style.display = 'block';
        observacionsContainer.querySelector('textarea').required = true;
    });

    btnDocuments.addEventListener('click', function(e) {
        estatSelect.value = 'documents_requerits';
        observacionsContainer.style.display = 'block';
        observacionsContainer.querySelector('textarea').required = true;
    });

    form.addEventListener('submit', function(e) {
        if (estatSelect.value === '') {
            e.preventDefault();
            alert('Has de triar un estat per resoldre la sol·licitud.');
            estatSelect.focus();
        }
    });
});
</script>
@endpush
