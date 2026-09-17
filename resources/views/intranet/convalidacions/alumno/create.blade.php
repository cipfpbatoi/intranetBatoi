@extends('layouts.intranet')

@section('titulo', 'Nova convalidació')

@section('content')
<div class="container">
    <h1>Nova sol·licitud de convalidació</h1>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Pots sol·licitar convalidació dels mòduls del cicle en què estàs matriculat.
        per cada mòdul, pots triar la raó de convalidació:
    </div>

    <form id="convalidacio-form" method="POST" action="{{ route('convalidacions.store') }}">
        @csrf

        <div class="card mt-3">
            <div class="card-body">
                <h5>Selecciona els mòduls a convalidar:</h5>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Important:</strong> Si adjuntes un certificat, has d'indicar que la informació és autèntica.
                </div>
                
                <div id="moduls-container" class="mb-3">
                    @foreach ($modulsDisponibles as $modulo)
                        <div class="form-check mb-2" data-modulo-id="{{ $modulo->codigo }}">
                            <input class="form-check-input modulo-checkbox" type="checkbox" 
                                   name="items[]" value="{{ $modulo->codigo }}" 
                                   data-modulo-id="{{ $modulo->codigo }}"
                                   id="modulo-{{ $modulo->codigo }}">
                            <label class="form-check-label" for="modulo-{{ $modulo->codigo }}">
                                <strong>{{ $modulo->literal }}</strong>
                            </label>
                            
                            <div class="ms-4 mt-2 d-none modulo-options" id="options-{{ $modulo->codigo }}">
                                <div class="mb-2">
                                    <label class="form-label">Tipus de convalidació:</label>
                                    <select class="form-select tipus-convalidacio" 
                                            name="items[{{ $modulo->codigo }}][tipus_convalidacio]"
                                            id="tipus-{{ $modulo->codigo }}">
                                        <option value="">Triar tipus...</option>
                                        <option value="mateix_centre">Mateix centre (cicle cursat)</option>
                                        <option value="altre_centre">Altre centre (certificat)</option>
                                        <option value="escola_idiomes">Escola d'idiomes (certificat)</option>
                                        <option value="titol_universitari">Títol universitari</option>
                                        <option value="titol_fp">Títol FP1 o FP2</option>
                                    </select>
                                </div>

                                <div class="mateix-centre-convalidacio" style="display: none;">
                                    <div class="mb-2">
                                        <label class="form-label">Cicle formatiu cursat:</label>
                                        <select class="form-select cicle-select" 
                                                name="items[{{ $modulo->codigo }}][cicle_formatiu_cursat_id]"
                                                id="cicle-{{ $modulo->codigo }}">
                                            <option value="">Triar cicle...</option>
                                            @foreach ($ciclesCursats as $cicle)
                                                <option value="{{ $cicle->id }}">
                                                    {{ $cicle->cicle?->literal ?? 'Cicle ' . $cicle->any_curs }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="certificat-convalidacio" style="display: none;">
                                    <div class="mb-2">
                                        <label class="form-label">Adjuntar certificat (PDF o imatge):</label>
                                        <input type="file" class="form-control certificat-file" 
                                               name="items[{{ $modulo->codigo }}][certificat_path]"
                                               id="certificat-{{ $modulo->codigo }}">
                                        <div class="form-text">Màxim 5 MB. Formats: PDF, JPG, PNG.</div>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input certificat-autentic" type="checkbox" 
                                               name="items[{{ $modulo->codigo }}][certificat_autentic]"
                                               id="autentic-{{ $modulo->codigo }}">
                                        <label class="form-check-label" for="autentic-{{ $modulo->codigo }}">
                                            Declaro que la informació del certificat és autèntica i que se m'hi pot sol·licitar.
                                        </label>
                                    </div>
                                </div>

                                <div class="titol-convalidacio" style="display: none; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">
                                    <i class="fas fa-times-circle text-danger"></i>
                                    <strong>Aquesta convalidació no es pot fer a través d'aquesta interfície.</strong>
                                    Per favor, contacta amb la secretaria per a més informació.
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="alert alert-danger d-none" id="errors-container"></div>

                <button type="submit" class="btn btn-primary mt-3" id="submit-btn">
                    <i class="fas fa-save"></i> Enviar sol·licitud
                </button>
                <a href="{{ route('convalidacions.index') }}" class="btn btn-secondary mt-3">
                    <i class="fas fa-times"></i> Cancel·lar
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.modulo-checkbox');
    const submitBtn = document.getElementById('submit-btn');
    let selectedCount = 0;

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const moduloId = this.dataset.moduloId;
            const options = document.getElementById(`options-${moduloId}`);
            const tipusSelect = document.getElementById(`tipus-${moduloId}`);
            
            if (this.checked) {
                options.classList.remove('d-none');
                selectedCount++;
            } else {
                options.classList.add('d-none');
                selectedCount--;
                tipusSelect.value = '';
                document.getElementById(`cicle-${moduloId}`).value = '';
                document.getElementById(`certificat-${moduloId}`).value = '';
                document.getElementById(`autentic-${moduloId}`).checked = false;
            }

            submitBtn.disabled = selectedCount === 0;
            submitBtn.innerText = selectedCount > 0 
                ? `Enviar sol·licitud (${selectedCount} mòdul${selectedCount > 1 ? 's' : ''})`
                : 'Selecciona almenys un mòdul';
        });
    });

    const tipusSelects = document.querySelectorAll('.tipus-convalidacio');
    tipusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const moduloId = this.id.split('-')[1];
            const mateixCentro = document.querySelector(`#options-${moduloId} .mateix-centre-convalidacio`);
            const certificat = document.querySelector(`#options-${moduloId} .certificat-convalidacio`);
            const titol = document.querySelector(`#options-${moduloId} .titol-convalidacio`);

            if (this.value === 'mateix_centre') {
                mateixCentro.style.display = 'block';
                certificat.style.display = 'none';
                titol.style.display = 'none';
            } else if (this.value === 'altre_centre' || this.value === 'escola_idiomes') {
                mateixCentro.style.display = 'none';
                certificat.style.display = 'block';
                titol.style.display = 'none';
            } else if (this.value === 'titol_universitari' || this.value === 'titol_fp') {
                mateixCentro.style.display = 'none';
                certificat.style.display = 'none';
                titol.style.display = 'block';
            } else {
                mateixCentro.style.display = 'none';
                certificat.style.display = 'none';
                titol.style.display = 'none';
            }
        });
    });

    const certificatFiles = document.querySelectorAll('.certificat-file');
    certificatFiles.forEach(input => {
        input.addEventListener('change', async function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
                
                if (!validTypes.includes(file.type)) {
                    alert('Només es poden adjuntar fitxers PDF, JPG o PNG.');
                    this.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('El fitxer supera els 5 MB.');
                    this.value = '';
                    return;
                }

                const formData = new FormData();
                formData.append('file', file);

                try {
                    const response = await fetch('{{ route("convalidacions.upload-certificat") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    
                    if (!response.ok) {
                        alert(data.error || 'Error al pujar el fitxer.');
                        this.value = '';
                        return;
                    }

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = this.name;
                    hiddenInput.value = data.path;
                    this.parentNode.appendChild(hiddenInput);
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al pujar el fitxer.');
                    this.value = '';
                }
            }
        });
    });

    document.getElementById('convalidacio-form').addEventListener('submit', function(e) {
        const errors = [];
        
        document.querySelectorAll('.modulo-checkbox:checked').forEach(checkbox => {
            const moduloId = checkbox.dataset.moduloId;
            const tipusSelect = document.getElementById(`tipus-${moduloId}`);
            const tipusValue = tipusSelect.value;
            const certificatInput = document.getElementById(`certificat-${moduloId}`);
            const autenticCheckbox = document.getElementById(`autentic-${moduloId}`);

            if (tipusValue === 'mateix_centre') {
                const cicleSelect = document.getElementById(`cicle-${moduloId}`);
                if (cicleSelect.value === '') {
                    errors.push(`El mòdul ${checkbox.parentElement.querySelector('label').innerText.trim()} requereix un cicle formatiu cursat.`);
                }
            } else if (tipusValue === 'altre_centre' || tipusValue === 'escola_idiomes') {
                if (certificatInput.files.length === 0) {
                    errors.push(`El mòdul ${checkbox.parentElement.querySelector('label').innerText.trim()} requereix un certificat adjunt.`);
                } else if (!autenticCheckbox.checked) {
                    errors.push(`Has d'indicar que la informació és autèntica per al mòdul ${checkbox.parentElement.querySelector('label').innerText.trim()}.`);
                }
            } else if (tipusValue === 'titol_universitari' || tipusValue === 'titol_fp') {
                errors.push(`El mòdul ${checkbox.parentElement.querySelector('label').innerText.trim()} no es pot convalidar a través d'aquesta interfície.`);
            }
        });

        if (errors.length > 0) {
            e.preventDefault();
            const errorsContainer = document.getElementById('errors-container');
            errorsContainer.innerHTML = errors.map(error => `<div>${error}</div>`).join('');
            errorsContainer.classList.remove('d-none');
            window.scrollTo(0, errorsContainer.offsetTop - 100);
        }
    });
});
</script>
@endpush
