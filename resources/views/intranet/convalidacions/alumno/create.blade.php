@extends('layouts.intranet')

@section('titulo', 'Nova sol·licitud de convalidació')

@section('content')
<div class="container">
    <h1>Nova sol·licitud de convalidació</h1>
    <p>Selecciona un o més mòduls. Abans de tramitar veuràs totes les peticions marcades en el resum inferior.</p>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('convalidacions.store') }}" enctype="multipart/form-data" id="convalidacio-form">
        @csrf
        <input type="hidden" name="submission_token" value="{{ $submissionToken }}">

        @foreach ($modulsDisponibles as $index => $modulo)
            <div class="card mb-3 peticio" data-label="{{ $modulo->literal }}">
                <div class="card-header">
                    <label class="mb-0"><input type="checkbox" class="activar-peticio"> {{ $modulo->literal }}</label>
                </div>
                <fieldset class="card-body" disabled>
                    <input type="hidden" name="items[{{ $index }}][modulo_destino_id]" value="{{ $modulo->codigo }}">
                    <label class="form-label" for="origen-{{ $index }}">Origen</label>
                    <select class="form-select origen" id="origen-{{ $index }}" name="items[{{ $index }}][origen]" required>
                        <option value="">Selecciona una opció</option>
                        @foreach ($origens as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                        <option value="secretaria">Títol universitari o FP1/FP2</option>
                    </select>

                    <div class="propi-centre mt-3 d-none">
                        <label class="form-label">Mòdul o estudi previ al centre</label>
                        <select class="form-select" name="items[{{ $index }}][modulo_origen_id]">
                            <option value="">Selecciona el mòdul origen</option>
                            @foreach ($modulsPrevis as $previ)<option value="{{ $previ->codigo }}">{{ $previ->literal }}</option>@endforeach
                        </select>
                    </div>

                    <div class="extern mt-3 d-none">
                        <label class="form-label">Document (PDF, JPG, JPEG o PNG; màxim {{ round($maxDocumentKb / 1024, 1) }} MB)</label>
                        <input type="file" class="form-control" name="items[{{ $index }}][document]" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" name="items[{{ $index }}][declaracio_responsable]" value="1" id="declaracio-{{ $index }}">
                            <label class="form-check-label" for="declaracio-{{ $index }}">Declare que la informació aportada és original i que dispose dels originals en cas que se'm demanen.</label>
                        </div>
                    </div>
                    <div class="secretaria alert alert-warning mt-3 d-none">Este tipus de convalidació s'ha de consultar amb Secretaria i no es pot afegir ací.</div>
                </fieldset>
            </div>
        @endforeach

        <div class="card mb-3"><div class="card-body"><h2 class="h5">Resum</h2><ul id="resum" class="mb-0"><li>Encara no has afegit cap mòdul.</li></ul></div></div>
        <button class="btn btn-primary" type="submit">Tramitar sol·licitud</button>
        <a class="btn btn-secondary" href="{{ route('convalidacions.index') }}">Cancel·lar</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.peticio').forEach((card) => {
    const active = card.querySelector('.activar-peticio');
    const fieldset = card.querySelector('fieldset');
    const origin = card.querySelector('.origen');
    const own = card.querySelector('.propi-centre');
    const external = card.querySelector('.extern');
    const secretaria = card.querySelector('.secretaria');
    const refresh = () => {
        fieldset.disabled = !active.checked;
        const value = origin.value;
        own.classList.toggle('d-none', value !== 'propi_centre');
        external.classList.toggle('d-none', !value || value === 'propi_centre' || value === 'secretaria');
        secretaria.classList.toggle('d-none', value !== 'secretaria');
        document.getElementById('resum').innerHTML = [...document.querySelectorAll('.peticio')]
            .filter((item) => item.querySelector('.activar-peticio').checked && item.querySelector('.origen').value !== 'secretaria')
            .map((item) => `<li>${item.dataset.label}</li>`).join('') || '<li>Encara no has afegit cap mòdul.</li>';
    };
    active.addEventListener('change', refresh);
    origin.addEventListener('change', refresh);
});
document.getElementById('convalidacio-form').addEventListener('submit', (event) => {
    document.querySelectorAll('.peticio').forEach((card) => {
        if (card.querySelector('.origen').value === 'secretaria') card.querySelector('fieldset').disabled = true;
    });
    if (!document.querySelector('.peticio fieldset:not([disabled])')) {
        event.preventDefault();
        window.alert('Has d\'afegir almenys un mòdul vàlid.');
    }
});
</script>
@endpush
