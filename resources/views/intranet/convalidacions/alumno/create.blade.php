@extends('layouts.intranet')

@section('titulo', 'Nova sol·licitud de convalidació')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="mb-1">Nova sol·licitud</h1>
        <p class="text-muted mb-0">Afig els mòduls que vols convalidar i revisa el conjunt abans de tramitar-lo.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('convalidacions.store') }}" enctype="multipart/form-data" id="convalidacio-form">
        @csrf
        <input type="hidden" name="submission_token" value="{{ $submissionToken }}">

        <div class="card mb-3" id="sollicitud-composicio">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <strong class="d-block">Mòduls de la sol·licitud</strong>
                    <small class="text-muted">Només es tramitaran els mòduls que apareguen en esta llista.</small>
                </div>
                <button
                    class="btn btn-primary"
                    id="obrir-afegir-modul"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#afegir-modul-modal"
                    @disabled($modulsDisponibles->isEmpty())
                >
                    <i class="fa fa-plus" aria-hidden="true"></i> Afegir mòdul
                </button>
            </div>

            <div class="card-body">
                @if ($modulsDisponibles->isEmpty())
                    <div class="alert alert-info mb-0">No tens cap mòdul disponible per a una nova sol·licitud.</div>
                @else
                    <div id="builder-feedback" class="visually-hidden" role="status" aria-live="polite"></div>
                    <div id="sollicitud-buida" class="text-muted text-center py-5">
                        <i class="fa fa-list-alt fa-2x mb-3 d-block" aria-hidden="true"></i>
                        <p class="mb-1">Encara no has afegit cap mòdul.</p>
                        <small>Prem «Afegir mòdul» per començar a preparar la sol·licitud.</small>
                    </div>
                    <div class="table-responsive" id="sollicitud-taula" hidden>
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Mòdul a convalidar</th>
                                    <th scope="col">Acreditació</th>
                                    <th scope="col" class="text-end">Accions</th>
                                </tr>
                            </thead>
                            <tbody id="peticions"></tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="fw-semibold" id="sollicitud-count" aria-live="polite">Total de mòduls a convalidar: 0</span>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-secondary" id="cancel-sollicitud" href="{{ route('convalidacions.index') }}">Cancel·lar</a>
                    <button
                        class="btn btn-primary"
                        id="revisar-sollicitud"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#revisar-sollicitud-modal"
                        disabled
                    >
                        Revisar sol·licitud
                    </button>
                </div>
            </div>
        </div>

        @if (!$modulsDisponibles->isEmpty())
            <div class="modal fade" id="afegir-modul-modal" tabindex="-1" aria-labelledby="afegir-modul-titol" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="afegir-modul-titol">Afegir mòdul a la sol·licitud</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tancar"></button>
                        </div>
                        <div class="modal-body">
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
                                <label class="form-label" for="builder-resultat">Mòdul superat</label>
                                <select class="form-select" id="builder-resultat">
                                    <option value="">Selecciona un mòdul aprovat</option>
                                    @foreach ($modulsAprovats as $resultat)
                                        <option
                                            value="{{ $resultat['id'] }}"
                                            data-codi="{{ $resultat['modul'] }}"
                                            data-modul="{{ $resultat['nom_modul'] ?: $resultat['modul'] }}"
                                            data-cicle-nom="{{ $resultat['nom_cicle'] ?: $resultat['cicle'] }}"
                                            data-any="{{ $resultat['any'] }}"
                                            data-nota="{{ number_format($resultat['nota'], 0, ',', '') }}"
                                            data-es-fol="{{ $resultat['fol_logse_catalog'] ? '1' : '0' }}"
                                            data-fol-logse="{{ $resultat['fol_logse_catalog'] ? '1' : '' }}"
                                            data-fol-cicle="{{ $resultat['fol_logse_cicle'] ?? '' }}"
                                            data-fol-nivell="{{ $resultat['fol_logse_nivell'] ?? '' }}"
                                        >
                                            {{ $resultat['modul'] }} — {{ $resultat['nom_modul'] ?: $resultat['modul'] }} —
                                            {{ $resultat['nom_cicle'] ?: $resultat['cicle'] }} ·
                                            Any {{ $resultat['any'] }} ·
                                            Nota {{ number_format($resultat['nota'], 0, ',', '') }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="border rounded bg-light p-3 mt-2" id="resultat-detall" hidden>
                                    <dl class="row g-2 mb-0 small">
                                        <dt class="col-sm-3">Mòdul</dt><dd class="col-sm-9 mb-0" id="resultat-detall-modul"></dd>
                                        <dt class="col-sm-3">Cicle</dt><dd class="col-sm-9 mb-0" id="resultat-detall-cicle"></dd>
                                        <dt class="col-sm-3">Any d'aprovació</dt><dd class="col-sm-9 mb-0" id="resultat-detall-any"></dd>
                                        <dt class="col-sm-3">Nota</dt><dd class="col-sm-9 mb-0" id="resultat-detall-resultat"></dd>
                                    </dl>
                                </div>
                                @if ($modulsAprovats === [])
                                    <div class="form-text text-warning">No s'han trobat mòduls aprovats disponibles per a seleccionar.</div>
                                @endif
                            </div>

                            <div id="fol-logse-section" class="border rounded p-3 mb-3" hidden>
                                <label class="form-label" for="builder-fol-logse">Revisa el certificat acadèmic o l’expedient: els estudis d’origen són LOGSE? La resposta és responsabilitat teua.</label>
                                <select class="form-select" id="builder-fol-logse">
                                    <option value="">Selecciona sí o no</option>
                                    <option value="1">Sí, són LOGSE</option>
                                    <option value="0">No, no són LOGSE</option>
                                </select>
                                <div id="fol-logse-catalog-notice" class="form-text" hidden>El codi d’este mòdul apareix al catàleg FOL LOGSE. Confirma la informació revisant el teu expedient.</div>
                                <div id="fol-logse-document-notice" class="form-text text-warning" hidden>Si indiques que els estudis són LOGSE, adjunta el certificat corresponent. Eres responsable de comprovar que presentes la documentació necessària; Direcció revisarà els fitxers.</div>
                            </div>

                            <div id="fol-origen-section" class="mb-3" hidden>
                                <label class="form-label" for="builder-es-fol">Estàs demanant esta convalidació a partir del mòdul Formació i Orientació Laboral (FOL)?</label>
                                <select class="form-select" id="builder-es-fol">
                                    <option value="">Selecciona sí o no</option>
                                    <option value="1">Sí, és FOL</option>
                                    <option value="0">No és FOL</option>
                                </select>
                            </div>

                            <div id="documents-builder" class="mb-3">
                                <label class="form-label">Documents de suport (màxim 3)</label>
                                <p class="form-text">Pots adjuntar documents en qualsevol modalitat. En cada fitxer indica de quin document es tracta.</p>
                                <div id="builder-documents-list"></div>
                                <button class="btn btn-sm btn-outline-secondary mt-2" type="button" id="add-document">Afegir document</button>
                                <div class="form-text">PDF, JPG, JPEG o PNG; màxim {{ round($maxDocumentKb / 1024, 1) }} MB per fitxer.</div>
                            </div>

                            <div id="avis-secretaria" class="alert alert-warning" hidden>
                                Este tipus de convalidació no es tramita mitjançant este formulari. Consulta amb Secretaria el procediment que correspon.
                            </div>

                            <div id="builder-error" class="alert alert-danger mb-0" role="alert" hidden></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tancar</button>
                            <button class="btn btn-primary" id="add-peticio" type="button" hidden>
                                Afegir a la sol·licitud
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="modal fade" id="revisar-sollicitud-modal" tabindex="-1" aria-labelledby="revisar-sollicitud-titol" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="revisar-sollicitud-titol">Revisa la sol·licitud</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tancar"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Comprova que tots els mòduls i les acreditacions són correctes abans de tramitar.</p>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Mòdul a convalidar</th>
                                        <th scope="col">Acreditació</th>
                                    </tr>
                                </thead>
                                <tbody id="revisio-peticions"></tbody>
                            </table>
                        </div>
                        <div class="form-check mt-3" id="declaracio-responsable-sollicitud-group" hidden>
                            <input type="checkbox" class="form-check-input" id="declaracio-responsable-sollicitud" name="declaracio_responsable_sollicitud" value="1">
                            <label class="form-check-label" for="declaracio-responsable-sollicitud">Declare que la informació aportada és original i que dispose dels originals en cas que se'm demanen. Esta declaració s'aplica a tots els documents de la sol·licitud.</label>
                        </div>
        <div class="text-danger small mt-2" id="declaracio-responsable-error" role="alert" hidden>Accepta la declaració responsable abans de continuar.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tornar i modificar</button>
                        <button class="btn btn-primary" id="presentar-sollicitud" type="button">Presentar sol·licitud</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmar-presentacio-modal" tabindex="-1" aria-labelledby="confirmar-presentacio-titol" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="confirmar-presentacio-titol">Presentar sol·licitud</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tancar"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Vas a presentar la sol·licitud. Després no podràs modificar els mòduls ni les acreditacions. Vols continuar?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tornar al resum</button>
                        <button class="btn btn-primary" id="confirmar-presentacio" type="submit">Sí, presentar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
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
    const cancelLink = document.getElementById('cancel-sollicitud');
    cancelLink?.addEventListener('click', (event) => {
        if (!window.confirm('Vols cancel·lar la sol·licitud? Es perdrà la composició actual.')) {
            event.preventDefault();
        }
    });

    const moduleSelect = document.getElementById('builder-modulo');
    if (!moduleSelect) return;

    const addModalElement = document.getElementById('afegir-modul-modal');
    const reviewModalElement = document.getElementById('revisar-sollicitud-modal');
    const confirmationModalElement = document.getElementById('confirmar-presentacio-modal');
    const originSelect = document.getElementById('builder-origen');
    const resultSelect = document.getElementById('builder-resultat');
    const resultDetail = document.getElementById('resultat-detall');
    const resultDetailModule = document.getElementById('resultat-detall-modul');
    const resultDetailCycle = document.getElementById('resultat-detall-cicle');
    const resultDetailYear = document.getElementById('resultat-detall-any');
    const resultDetailResult = document.getElementById('resultat-detall-resultat');
    const declaration = document.getElementById('declaracio-responsable-sollicitud');
    const declarationGroup = document.getElementById('declaracio-responsable-sollicitud-group');
    const declarationError = document.getElementById('declaracio-responsable-error');
    const folSection = document.getElementById('fol-logse-section');
    const folSelect = document.getElementById('builder-fol-logse');
    const folCatalogNotice = document.getElementById('fol-logse-catalog-notice');
    const folDocumentNotice = document.getElementById('fol-logse-document-notice');
    const folOriginSection = document.getElementById('fol-origen-section');
    const folOriginSelect = document.getElementById('builder-es-fol');
    const documentsList = document.getElementById('builder-documents-list');
    const addDocumentButton = document.getElementById('add-document');
    const originGroup = document.getElementById('origen-group');
    const ownCenter = document.getElementById('propi-centre');
    const secretary = document.getElementById('avis-secretaria');
    const error = document.getElementById('builder-error');
    const feedback = document.getElementById('builder-feedback');
    const addButton = document.getElementById('add-peticio');
    const form = document.getElementById('convalidacio-form');
    const requests = document.getElementById('peticions');
    const requestsTable = document.getElementById('sollicitud-taula');
    const emptySummary = document.getElementById('sollicitud-buida');
    const summaryCount = document.getElementById('sollicitud-count');
    const reviewButton = document.getElementById('revisar-sollicitud');
    const presentButton = document.getElementById('presentar-sollicitud');
    const confirmPresentationButton = document.getElementById('confirmar-presentacio');
    const reviewRequests = document.getElementById('revisio-peticions');
    const originLabels = @json($origens);
    const externalOrigins = ['altre_centre', 'certificat_eoi'];
    const maxDocumentBytes = {{ $maxDocumentKb * 1024 }};
    const maxDocumentMegabytes = {{ round($maxDocumentKb / 1024, 1) }};
    let documentRows = [];
    let nextIndex = 0;
    let openingPresentationConfirmation = false;
    let presentationConfirmed = false;

    const selectedText = (select) => select.options[select.selectedIndex]?.text.trim() || '';
    const setError = (message = '') => {
        error.textContent = message;
        error.hidden = message === '';
    };
    const setFeedback = (message = '') => {
        feedback.textContent = message;
    };
    const addHidden = (container, name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        container.appendChild(input);
    };
    const refreshResultDetail = () => {
        const option = resultSelect.options[resultSelect.selectedIndex];
        const visible = originSelect.value === 'propi_centre' && option?.value !== '';
        resultDetail.hidden = !visible;
        if (!visible) return;

        fillModuleReference(resultDetailModule, option.dataset.codi, option.dataset.modul);
        fillCycleName(resultDetailCycle, option.dataset.cicleNom);
        resultDetailYear.textContent = option.dataset.any;
        resultDetailResult.textContent = option.dataset.nota;
    };
    const refreshBuilder = () => {
        const hasModule = moduleSelect.value !== '';
        const origin = originSelect.value;
        originGroup.hidden = !hasModule;
        ownCenter.hidden = !hasModule || origin !== 'propi_centre';
        secretary.hidden = !hasModule || origin !== 'secretaria';
        const isIpeI = moduleSelect.value === '1709';
        const selectedResult = resultSelect.options[resultSelect.selectedIndex];
        const ownFol = isIpeI && origin === 'propi_centre' && selectedResult?.dataset.esFol === '1';
        const folApplicable = isIpeI && hasModule && origin !== '' && origin !== 'secretaria';
        const xmlClassification = ownFol ? selectedResult.dataset.folLogse : '';
        folSection.hidden = !folApplicable;
        folCatalogNotice.hidden = !(ownFol && xmlClassification !== '');
        if (!folApplicable) folSelect.value = '';
        else if (ownFol && xmlClassification !== '') folSelect.value = xmlClassification;
        const isFolOrigin = origin === 'propi_centre' ? ownFol : folOriginSelect.value === '1';
        folDocumentNotice.hidden = !folApplicable || folSelect.value !== '1' || !isFolOrigin;
        folOriginSection.hidden = !isIpeI || origin !== 'altre_centre';
        document.getElementById('documents-builder').hidden = !hasModule || origin === '' || origin === 'secretaria';
        addDocumentButton.disabled = documentRows.length >= 3;
        addButton.hidden = !hasModule || origin === '' || origin === 'secretaria';
        refreshResultDetail();
        setError();
    };
    const refreshSummary = () => {
        const count = requests.children.length;
        const hasRequests = count > 0;
        const hasDeclarationItems = [...requests.children].some((item) => item.dataset.requiresDeclaration === 'true');
        emptySummary.hidden = hasRequests;
        requestsTable.hidden = !hasRequests;
        reviewButton.disabled = !hasRequests;
        declarationGroup.hidden = !hasDeclarationItems;
        if (!hasDeclarationItems) {
            declaration.checked = false;
            declarationError.hidden = true;
        }
        summaryCount.textContent = `Total de mòduls a convalidar: ${count}`;
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
        originSelect.value = '';
        resultSelect.value = '';
        declaration.checked = false;
        documentRows = [];
        documentsList.replaceChildren();
        folSelect.value = '';
        folOriginSelect.value = '';
        addDocumentRow();
        refreshBuilder();
    };
    const formatCode = (code) => code.replace(/^([A-Za-z]+)(\d+)$/, '$1 $2');
    const appendLine = (container, value, className = '') => {
        const line = document.createElement('div');
        line.className = className;
        line.textContent = value;
        container.appendChild(line);
    };
    const fillModuleReference = (container, code, name) => {
        container.replaceChildren();
        const moduleElement = document.createElement('strong');
        moduleElement.textContent = formatCode(code);
        if (name && name !== code) {
            moduleElement.append(` — ${name}`);
        }
        container.appendChild(moduleElement);
    };
    const appendModuleLine = (container, code, name, className = '') => {
        const line = document.createElement('div');
        line.className = className;
        fillModuleReference(line, code, name);
        container.appendChild(line);
    };
    const fillCycleName = (container, name) => {
        container.replaceChildren();
        const nameElement = document.createElement('em');
        nameElement.textContent = name;
        container.appendChild(nameElement);
    };
    const fillModuleCell = (cell, item) => {
        cell.replaceChildren();
        appendModuleLine(cell, item.dataset.moduleId, item.dataset.moduleLabel);
    };
    const fillAccreditationCell = (cell, item) => {
        const inputs = [...cell.querySelectorAll('input')];
        cell.replaceChildren(...inputs);
        if (item.dataset.accreditationSourceCode) {
            appendModuleLine(
                cell,
                item.dataset.accreditationSourceCode,
                item.dataset.accreditationSourceName,
                'mb-1'
            );
            const cycle = document.createElement('div');
            cycle.className = 'mb-1 fst-italic';
            cycle.textContent = item.dataset.accreditationCycleName;
            cell.appendChild(cycle);
            appendLine(cell, `Any ${item.dataset.accreditationYear} · Nota ${item.dataset.accreditationNote}`, 'small mb-1');
            appendLine(cell, item.dataset.modality, 'text-muted small fst-italic');
            JSON.parse(item.dataset.documents || '[]').forEach((doc) => appendLine(cell, `${doc.descripcio}: ${doc.nom}`, 'small mt-1'));
            return;
        }
        appendLine(cell, item.dataset.accreditationTitle, 'fw-semibold mb-1');
        appendLine(cell, item.dataset.accreditationMeta, 'small');
        JSON.parse(item.dataset.documents || '[]').forEach((doc) => appendLine(cell, `${doc.descripcio}: ${doc.nom}`, 'small mt-1'));
    };
    const addDocumentRow = () => {
        if (documentRows.length >= 3) return;
        const row = document.createElement('div');
        row.className = 'border rounded p-2 mb-2';
        const description = document.createElement('input');
        description.type = 'text';
        description.className = 'form-control mb-2';
        description.maxLength = 120;
        description.placeholder = 'Exemples: certificat acadèmic, expedient o certificat PRL';
        description.setAttribute('aria-label', 'Descripció del document');
        const file = document.createElement('input');
        file.type = 'file';
        file.className = 'form-control';
        file.accept = '.pdf,.jpg,.jpeg,.png';
        file.setAttribute('aria-label', 'Fitxer adjunt');
        file.addEventListener('change', () => {
            const selectedFile = file.files[0];
            setError(selectedFile && selectedFile.size > maxDocumentBytes
                ? `El fitxer supera el límit de ${maxDocumentMegabytes} MB. Tria un fitxer més menut.`
                : '');
        });
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-sm btn-link text-danger px-0';
        remove.textContent = 'Llevar document';
        remove.addEventListener('click', () => {
            documentRows = documentRows.filter((entry) => entry.row !== row);
            row.remove();
            addDocumentButton.disabled = documentRows.length >= 3;
        });
        row.append(description, file, remove);
        documentsList.appendChild(row);
        documentRows.push({ row, description, file });
        addDocumentButton.disabled = documentRows.length >= 3;
    };
    const buildReview = () => {
        reviewRequests.replaceChildren();
        [...requests.children].forEach((item) => {
            const summary = document.createElement('tr');
            const moduleCell = document.createElement('td');
            const accreditationCell = document.createElement('td');
            fillModuleCell(moduleCell, item);
            fillAccreditationCell(accreditationCell, item);
            summary.append(moduleCell, accreditationCell);
            reviewRequests.appendChild(summary);
        });
    };
    const ensureDeclaration = (item, accepted) => {
        if (item.dataset.requiresDeclaration !== 'true') return;

        const name = `items[${item.dataset.index}][declaracio_responsable]`;
        let declarationInput = item.querySelector(`input[name="${name}"]`);
        if (!declarationInput) {
            declarationInput = document.createElement('input');
            declarationInput.type = 'hidden';
            declarationInput.name = name;
            item.querySelector('td:nth-child(2)')?.prepend(declarationInput);
        }
        declarationInput.value = accepted ? '1' : '0';
    };

    moduleSelect.addEventListener('change', refreshBuilder);
    originSelect.addEventListener('change', refreshBuilder);
    resultSelect.addEventListener('change', refreshResultDetail);
    folSelect.addEventListener('change', refreshBuilder);
    folOriginSelect.addEventListener('change', refreshBuilder);
    addDocumentButton.addEventListener('click', addDocumentRow);
    reviewModalElement.addEventListener('show.bs.modal', buildReview);
    reviewModalElement.addEventListener('hidden.bs.modal', () => {
        if (!openingPresentationConfirmation) return;

        openingPresentationConfirmation = false;
        window.bootstrap?.Modal.getOrCreateInstance(confirmationModalElement).show();
    });
    confirmationModalElement.addEventListener('hidden.bs.modal', () => {
        if (!presentationConfirmed) {
            window.bootstrap?.Modal.getOrCreateInstance(reviewModalElement).show();
        }
    });
    presentButton.addEventListener('click', () => {
        const hasItemsRequiringDeclaration = [...requests.children].some((item) => item.dataset.requiresDeclaration === 'true');
        if (hasItemsRequiringDeclaration && !declaration.checked) {
            declarationError.hidden = false;
            declaration.focus();
            return;
        }

        declarationError.hidden = true;
        openingPresentationConfirmation = true;
        window.bootstrap?.Modal.getOrCreateInstance(reviewModalElement).hide();
    });
    declaration.addEventListener('change', () => {
        if (declaration.checked) declarationError.hidden = true;
    });
    confirmPresentationButton.addEventListener('click', () => {
        presentationConfirmed = true;
    });
    addModalElement.addEventListener('hidden.bs.modal', () => document.getElementById('obrir-afegir-modul')?.focus());

    addButton.addEventListener('click', () => {
        const moduleId = moduleSelect.value;
        const origin = originSelect.value;

        if (origin === 'propi_centre' && resultSelect.value === '') {
            setError('Selecciona el mòdul superat que vols aportar.');
            return;
        }
        const attached = documentRows.filter((entry) => entry.file.files.length > 0);
        if (attached.some((entry) => entry.file.files[0].size > maxDocumentBytes)) {
            setError(`Cada fitxer ha de tindre una mida màxima de ${maxDocumentMegabytes} MB. Tria fitxers més menuts.`);
            return;
        }
        if (externalOrigins.includes(origin) && !attached.length) {
            setError('Adjunta almenys un document per a esta modalitat.');
            return;
        }
        if (documentRows.some((entry) => entry.file.files.length && !entry.description.value.trim())) {
            setError('Indica de quin tipus és cada document adjunt.');
            return;
        }
        const isIpeI = moduleId === '1709';
        const result = origin === 'propi_centre' ? resultSelect.options[resultSelect.selectedIndex] : null;
        const folApplicable = isIpeI && origin !== 'secretaria';
        if (folApplicable && folSelect.value === '') {
            setError('Revisa el certificat acadèmic i indica si els estudis d’origen són LOGSE.');
            return;
        }
        if (isIpeI && origin === 'altre_centre' && folOriginSelect.value === '') {
            setError('Indica si l’origen és el mòdul FOL.');
            return;
        }
        const moduleLabel = selectedText(moduleSelect);
        const originLabel = originLabels[origin];
        const index = nextIndex++;
        const item = document.createElement('tr');
        item.dataset.moduleId = moduleId;
        item.dataset.moduleLabel = moduleLabel;
        item.dataset.originLabel = originLabel;
        item.dataset.index = String(index);

        const moduleCell = document.createElement('td');
        moduleCell.className = 'w-25';
        const accreditationCell = document.createElement('td');
        const actionsCell = document.createElement('td');
        actionsCell.className = 'text-end';
        addHidden(accreditationCell, `items[${index}][modulo_destino_id]`, moduleId);
        addHidden(accreditationCell, `items[${index}][origen]`, origin);

        const docsSummary = [];
        if (origin === 'propi_centre') {
            item.dataset.accreditationSourceCode = result.dataset.codi;
            item.dataset.accreditationSourceName = result.dataset.modul;
            item.dataset.accreditationCycleName = result.dataset.cicleNom;
            item.dataset.accreditationYear = result.dataset.any;
            item.dataset.accreditationNote = result.dataset.nota;
            item.dataset.modality = originLabel;
            addHidden(accreditationCell, `items[${index}][resultat_origen_id]`, resultSelect.value);
        } else {
            item.dataset.accreditationTitle = originLabel;
            item.dataset.accreditationMeta = attached.map((entry) => entry.file.files[0].name).join(', ');
            item.dataset.modality = '';
        }
        if (origin !== 'propi_centre' || attached.length > 0) item.dataset.requiresDeclaration = 'true';
        if (folApplicable) addHidden(accreditationCell, `items[${index}][fol_logse]`, folSelect.value);
        if (isIpeI && origin === 'altre_centre') addHidden(accreditationCell, `items[${index}][modulo_origen_es_fol]`, folOriginSelect.value);
        attached.forEach((entry, docIndex) => {
            const description = entry.description.value.trim();
            const clone = entry.file.cloneNode();
            entry.file.name = `items[${index}][documents][${docIndex}][fitxer]`;
            entry.file.hidden = true;
            accreditationCell.appendChild(entry.file);
            addHidden(accreditationCell, `items[${index}][documents][${docIndex}][descripcio]`, description);
            docsSummary.push({ descripcio: description, nom: entry.file.files[0].name });
            entry.row.replaceChildren(description, clone);
            entry.description = document.createElement('input');
        });
        item.dataset.documents = JSON.stringify(docsSummary);
        documentRows = [];
        documentsList.replaceChildren();
        addDocumentRow();
        if (folApplicable && origin === 'propi_centre') {
            item.dataset.folLogse = folSelect.value;
            item.dataset.folCicle = result?.dataset.folCicle || '';
            item.dataset.folNivell = result?.dataset.folNivell || '';
        }
        fillModuleCell(moduleCell, item);
        fillAccreditationCell(accreditationCell, item);

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-sm btn-outline-danger flex-shrink-0';
        remove.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i><span class="visually-hidden">Eliminar</span>';
        remove.addEventListener('click', () => {
            const option = [...moduleSelect.options].find((candidate) => candidate.value === item.dataset.moduleId);
            if (option) option.disabled = false;
            item.remove();
            declaration.checked = false;
            declarationError.hidden = true;
            refreshSummary();
            setFeedback(`S'ha eliminat ${item.dataset.moduleLabel} de la sol·licitud.`);
        });
        actionsCell.appendChild(remove);
        item.append(moduleCell, accreditationCell, actionsCell);
        requests.appendChild(item);

        moduleSelect.options[moduleSelect.selectedIndex].disabled = true;
        declaration.checked = false;
        declarationError.hidden = true;
        resetBuilder();
        refreshSummary();
        animateAddition(item);
        setFeedback(`S'ha afegit ${moduleLabel} a la sol·licitud.`);
        window.bootstrap?.Modal.getOrCreateInstance(addModalElement).hide();
    });

    form.addEventListener('submit', (event) => {
        if (!requests.children.length) {
            event.preventDefault();
            setFeedback('Has d\'afegir almenys un mòdul abans de tramitar.');
            return;
        }
        const declarationItems = [...requests.children].filter((item) => item.dataset.requiresDeclaration === 'true');
        if (declarationItems.length && !declaration.checked) {
            event.preventDefault();
            declarationError.hidden = false;
            window.bootstrap?.Modal.getOrCreateInstance(reviewModalElement).show();
            return;
        }
        declarationError.hidden = true;
        declarationItems.forEach((item) => ensureDeclaration(item, declaration.checked));
    });

    refreshBuilder();
    refreshSummary();
    addDocumentRow();
})();
</script>
@endpush
