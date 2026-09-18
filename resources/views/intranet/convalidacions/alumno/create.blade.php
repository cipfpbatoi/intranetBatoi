@extends('layouts.intranet')

@section('titulo', 'Nova sol·licitud de convalidació')

@section('content')
<div class="container">
    <h1>Nova sol·licitud de convalidació</h1>
    <p>Prepara cada petició per separat i comprova el resum abans de tramitar-la.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('convalidacions.store') }}" enctype="multipart/form-data" id="convalidacio-form">
        @csrf
        <input type="hidden" name="submission_token" value="{{ $submissionToken }}">

        <div class="row g-4 align-items-start" id="convalidacio-layout">
            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill">1</span>
                        <strong>Prepara una petició</strong>
                    </div>
                    <div class="card-body">
                @if ($modulsDisponibles->isEmpty())
                    <div class="alert alert-info mb-0">No tens cap mòdul disponible per a una nova sol·licitud.</div>
                @else
                    <div class="mb-3">
                        <label class="form-label" for="builder-modulo">Mòdul que vols convalidar</label>
                        <select class="form-select" id="builder-modulo">
                            <option value="">Selecciona un mòdul</option>
                            @foreach ($modulsDisponibles as $modulo)
                                <option value="{{ $modulo->codigo }}">{{ $modulo->literal }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="origen-group" class="mb-3" hidden>
                        <label class="form-label" for="builder-origen">Com vols justificar la convalidació?</label>
                        <select class="form-select" id="builder-origen">
                            <option value="">Selecciona una opció</option>
                            @foreach ($origens as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                            <option value="secretaria">Títol universitari o FP1/FP2</option>
                        </select>
                    </div>

                    <div id="propi-centre" class="mb-3" hidden>
                        <label class="form-label" for="builder-cicle">Estudi previ</label>
                        <select class="form-select" id="builder-cicle">
                            <option value="">Selecciona un cicle cursat</option>
                            @foreach ($ciclesPrevis as $cicle)<option value="{{ $cicle->id }}">{{ $cicle->literal }}</option>@endforeach
                        </select>
                        @if ($ciclesPrevis->isEmpty())
                            <div class="form-text text-warning">No consta cap cicle previ en el teu historial acadèmic.</div>
                        @endif
                    </div>

                    <div id="origen-extern" class="mb-3" hidden>
                        <label class="form-label" for="builder-document">Document acreditatiu</label>
                        <input type="file" class="form-control" id="builder-document" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">PDF, JPG, JPEG o PNG; màxim {{ round($maxDocumentKb / 1024, 1) }} MB.</div>
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="builder-declaracio" value="1">
                            <label class="form-check-label" for="builder-declaracio">Declare que la informació aportada és original i que dispose dels originals en cas que se'm demanen.</label>
                        </div>
                    </div>

                    <div id="avis-secretaria" class="alert alert-warning" hidden>
                        Este tipus de convalidació no es tramita mitjançant este formulari. Consulta amb Secretaria el procediment que correspon.
                    </div>

                    <div id="builder-error" class="alert alert-danger" hidden></div>
                    <div id="builder-feedback" class="alert alert-success" role="status" aria-live="polite" hidden></div>
                    <button class="btn btn-outline-primary" id="add-peticio" type="button" hidden>
                        <i class="fa fa-plus" aria-hidden="true"></i> Afegir a la sol·licitud
                    </button>
                @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-3 convalidacio-resum" id="resum-sollicitud">
                    <div class="card-header d-flex align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill">2</span>
                            <strong>Revisa i tramita</strong>
                        </div>
                        <span class="badge bg-secondary" id="resum-count" aria-live="polite">0 mòduls</span>
                    </div>
                    <div class="card-body">
                        <div id="resum-buit" class="text-muted py-3 text-center">
                            <i class="fa fa-list-alt fa-2x mb-2 d-block" aria-hidden="true"></i>
                            Els mòduls que afiges apareixeran ací sense substituir els anteriors.
                        </div>
                        <div class="list-group list-group-flush" id="peticions"></div>
                    </div>
                    <div class="card-footer d-flex flex-wrap gap-2">
                        <button class="btn btn-primary" id="tramitar" type="submit" disabled>Tramitar sol·licitud</button>
                        <a class="btn btn-secondary" href="{{ route('convalidacions.index') }}">Cancel·lar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
@media (min-width: 992px) {
    .convalidacio-resum {
        position: sticky;
        top: 1rem;
    }
}

@keyframes convalidacio-item-afegit {
    0% {
        opacity: 0;
        transform: translateY(-0.75rem);
        background-color: var(--bs-success-bg-subtle, #d1e7dd);
    }
    60% {
        opacity: 1;
        transform: translateY(0);
        background-color: var(--bs-success-bg-subtle, #d1e7dd);
    }
    100% {
        background-color: transparent;
    }
}

@keyframes convalidacio-comptador-actualitzat {
    50% {
        transform: scale(1.18);
    }
}

.convalidacio-item-nou {
    animation: convalidacio-item-afegit 700ms ease-out;
}

.convalidacio-comptador-actualitzat {
    animation: convalidacio-comptador-actualitzat 450ms ease-out;
}

@media (prefers-reduced-motion: reduce) {
    .convalidacio-item-nou,
    .convalidacio-comptador-actualitzat {
        animation: none;
    }
}
</style>
@endpush

@push('scripts')
<script>
(() => {
    const moduleSelect = document.getElementById('builder-modulo');
    if (!moduleSelect) return;

    const originSelect = document.getElementById('builder-origen');
    const cycleSelect = document.getElementById('builder-cicle');
    const declaration = document.getElementById('builder-declaracio');
    const originGroup = document.getElementById('origen-group');
    const ownCenter = document.getElementById('propi-centre');
    const external = document.getElementById('origen-extern');
    const secretary = document.getElementById('avis-secretaria');
    const error = document.getElementById('builder-error');
    const feedback = document.getElementById('builder-feedback');
    const addButton = document.getElementById('add-peticio');
    const requests = document.getElementById('peticions');
    const emptySummary = document.getElementById('resum-buit');
    const summaryCount = document.getElementById('resum-count');
    const submitButton = document.getElementById('tramitar');
    const originLabels = @json($origens);
    const externalOrigins = ['altre_centre', 'certificat_eoi', 'prl_logse'];
    let fileInput = document.getElementById('builder-document');
    let nextIndex = 0;

    const selectedText = (select) => select.options[select.selectedIndex]?.text || '';
    const setError = (message = '') => {
        error.textContent = message;
        error.hidden = message === '';
    };
    const setFeedback = (message = '') => {
        feedback.textContent = message;
        feedback.hidden = message === '';
    };
    const addHidden = (container, name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        container.appendChild(input);
    };
    const refreshBuilder = () => {
        const hasModule = moduleSelect.value !== '';
        const origin = originSelect.value;
        originGroup.hidden = !hasModule;
        ownCenter.hidden = !hasModule || origin !== 'propi_centre';
        external.hidden = !hasModule || !externalOrigins.includes(origin);
        secretary.hidden = !hasModule || origin !== 'secretaria';
        addButton.hidden = !hasModule || origin === '' || origin === 'secretaria';
        setError();
    };
    const refreshSummary = () => {
        const count = requests.children.length;
        const hasRequests = count > 0;
        emptySummary.hidden = hasRequests;
        submitButton.disabled = !hasRequests;
        summaryCount.textContent = `${count} ${count === 1 ? 'mòdul' : 'mòduls'}`;
        summaryCount.classList.toggle('bg-secondary', !hasRequests);
        summaryCount.classList.toggle('bg-success', hasRequests);
    };
    const animateAddition = (item) => {
        item.classList.add('convalidacio-item-nou');
        summaryCount.classList.remove('convalidacio-comptador-actualitzat');
        void summaryCount.offsetWidth;
        summaryCount.classList.add('convalidacio-comptador-actualitzat');
        item.addEventListener('animationend', () => item.classList.remove('convalidacio-item-nou'), { once: true });
    };
    const resetBuilder = () => {
        moduleSelect.value = '';
        declaration.checked = false;
        fileInput.value = '';
        refreshBuilder();
    };

    moduleSelect.addEventListener('change', refreshBuilder);
    originSelect.addEventListener('change', refreshBuilder);
    addButton.addEventListener('click', () => {
        const moduleId = moduleSelect.value;
        const origin = originSelect.value;

        if (origin === 'propi_centre' && cycleSelect.value === '') {
            setError('Selecciona el cicle que vols aportar com a estudi previ.');
            return;
        }
        if (externalOrigins.includes(origin) && (!fileInput.files.length || !declaration.checked)) {
            setError('Adjunta el document i accepta la declaració responsable.');
            return;
        }

        const moduleLabel = selectedText(moduleSelect);
        const index = nextIndex++;
        const item = document.createElement('div');
        item.className = 'list-group-item px-0 py-3';
        item.dataset.moduleId = moduleId;
        item.dataset.moduleLabel = moduleLabel;
        addHidden(item, `items[${index}][modulo_destino_id]`, moduleId);
        addHidden(item, `items[${index}][origen]`, origin);

        const header = document.createElement('div');
        header.className = 'd-flex justify-content-between align-items-start gap-3';
        const title = document.createElement('strong');
        title.textContent = moduleLabel;
        header.appendChild(title);

        const detail = document.createElement('div');
        detail.className = 'text-muted small';
        if (origin === 'propi_centre') {
            addHidden(item, `items[${index}][ciclo_origen_id]`, cycleSelect.value);
            detail.textContent = `${originLabels[origin]} · ${selectedText(cycleSelect)}`;
        } else {
            addHidden(item, `items[${index}][declaracio_responsable]`, '1');
            detail.textContent = `${originLabels[origin]} · ${fileInput.files[0].name}`;
            const replacement = fileInput.cloneNode();
            fileInput.removeAttribute('id');
            fileInput.name = `items[${index}][document]`;
            fileInput.hidden = true;
            item.appendChild(fileInput);
            fileInput = replacement;
            fileInput.id = 'builder-document';
            fileInput.removeAttribute('name');
            fileInput.hidden = false;
            external.insertBefore(fileInput, external.querySelector('.form-text'));
        }
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-sm btn-outline-danger flex-shrink-0';
        remove.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i><span class="visually-hidden">Eliminar</span>';
        remove.addEventListener('click', () => {
            const option = [...moduleSelect.options].find((candidate) => candidate.value === item.dataset.moduleId);
            if (option) option.disabled = false;
            item.remove();
            refreshSummary();
            setFeedback(`S'ha eliminat ${item.dataset.moduleLabel} del resum.`);
        });
        header.appendChild(remove);
        item.appendChild(header);
        item.appendChild(detail);
        requests.appendChild(item);

        moduleSelect.options[moduleSelect.selectedIndex].disabled = true;
        resetBuilder();
        refreshSummary();
        animateAddition(item);
        setFeedback(`S'ha afegit ${moduleLabel}. Pots triar un altre mòdul; el resum conserva els anteriors.`);
        moduleSelect.focus();
    });

    document.getElementById('convalidacio-form').addEventListener('submit', (event) => {
        if (!requests.children.length) {
            event.preventDefault();
            setError('Has d\'afegir almenys un mòdul abans de tramitar.');
        }
    });
    refreshBuilder();
    refreshSummary();
})();
</script>
@endpush
