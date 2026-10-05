@extends('layouts.intranet')

@section('titulo', $correspondencia->exists ? 'Editar correspondència d’anglés' : 'Afegir correspondència d’anglés')

@section('content')
<div class="container">
    <h1>{{ $correspondencia->exists ? 'Editar correspondència d’anglés' : 'Afegir correspondència d’anglés' }}</h1>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ $action }}" class="card card-body">
        @csrf
        @method($method)
        <fieldset class="mb-3">
            <legend class="h5">Cicle d’anglés (≥ 5 hores setmanals)</legend>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="codi-cicle-angles">Codi</label><input class="form-control" id="codi-cicle-angles" name="codi_cicle_angles" maxlength="50" required value="{{ old('codi_cicle_angles', $correspondencia->codi_cicle_angles) }}"></div>
                <div class="col-md-4"><label class="form-label" for="nom-cicle-angles-val">Nom en valencià</label><input class="form-control" id="nom-cicle-angles-val" name="nom_cicle_angles_val" maxlength="255" required value="{{ old('nom_cicle_angles_val', $correspondencia->nom_cicle_angles_val) }}"></div>
                <div class="col-md-4"><label class="form-label" for="nom-cicle-angles-cas">Nom en castellà</label><input class="form-control" id="nom-cicle-angles-cas" name="nom_cicle_angles_cas" maxlength="255" required value="{{ old('nom_cicle_angles_cas', $correspondencia->nom_cicle_angles_cas) }}"></div>
            </div>
        </fieldset>
        <fieldset class="mb-3">
            <legend class="h5">Cicle formatiu contenidor</legend>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="codi-cicle-contenidor">Codi</label><input class="form-control" id="codi-cicle-contenidor" name="codi_cicle_contenidor" maxlength="50" required value="{{ old('codi_cicle_contenidor', $correspondencia->codi_cicle_contenidor) }}"></div>
                <div class="col-md-4"><label class="form-label" for="nom-cicle-contenidor-val">Nom en valencià</label><input class="form-control" id="nom-cicle-contenidor-val" name="nom_cicle_contenidor_val" maxlength="255" required value="{{ old('nom_cicle_contenidor_val', $correspondencia->nom_cicle_contenidor_val) }}"></div>
                <div class="col-md-4"><label class="form-label" for="nom-cicle-contenidor-cas">Nom en castellà</label><input class="form-control" id="nom-cicle-contenidor-cas" name="nom_cicle_contenidor_cas" maxlength="255" required value="{{ old('nom_cicle_contenidor_cas', $correspondencia->nom_cicle_contenidor_cas) }}"></div>
            </div>
        </fieldset>
        <div class="mb-3">
            <label class="form-label" for="es-grau-superior">El cicle d’anglés és de grau superior?</label>
            <select class="form-select" id="es-grau-superior" name="es_grau_superior" required>
                <option value="1" @selected((string) old('es_grau_superior', $correspondencia->exists ? (int) $correspondencia->es_grau_superior : '') === '1')>Sí, grau superior</option>
                <option value="0" @selected((string) old('es_grau_superior', $correspondencia->exists ? (int) $correspondencia->es_grau_superior : '') === '0')>No</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('convalidacions.direction.angles.index') }}">Cancel·lar</a>
        </div>
    </form>
</div>
@endsection
