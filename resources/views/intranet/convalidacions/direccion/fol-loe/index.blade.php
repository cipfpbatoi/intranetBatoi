@extends('layouts.intranet')

@section('titulo', 'Mòduls FOL LOE')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="mb-1">Mòduls FOL LOE</h1>
            <p class="mb-0 text-muted">El codi s’utilitza per identificar resultats del centre i es compara sense zeros inicials.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('convalidacions.direction.configuracio') }}">Tornar a configuració</a>
    </div>

    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('convalidacions.direction.fol-loe.store') }}" class="card card-body mb-4">
        @csrf
        <h2 class="h5">Afegir mòdul</h2>
        <div class="row g-3">
            <div class="col-md-2"><label class="form-label" for="fol-loe-codi">Codi FOL</label><input class="form-control" id="fol-loe-codi" name="codigo" inputmode="numeric" maxlength="30" required value="{{ old('codigo') }}"></div>
            <div class="col-md-4"><label class="form-label" for="fol-loe-cas">Cicle en castellà</label><input class="form-control" id="fol-loe-cas" name="cicle_cas" maxlength="255" required value="{{ old('cicle_cas') }}"></div>
            <div class="col-md-4"><label class="form-label" for="fol-loe-val">Cicle en valencià</label><input class="form-control" id="fol-loe-val" name="cicle_val" maxlength="255" required value="{{ old('cicle_val') }}"></div>
            <div class="col-md-1"><label class="form-label" for="fol-loe-nivell">Nivell</label><select class="form-select" id="fol-loe-nivell" name="nivell" required><option value="GM">GM</option><option value="GS">GS</option></select></div>
            <div class="col-md-1 d-flex align-items-end"><button class="btn btn-primary" type="submit">Afegir</button></div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead><tr><th scope="col">Codi</th><th scope="col">Cicle (castellà)</th><th scope="col">Cicle (valencià)</th><th scope="col">Nivell</th><th scope="col">Accions</th></tr></thead>
            <tbody>
            @forelse ($moduls as $modul)
                <tr>
                    <td><label class="visually-hidden" for="codi-{{ $modul->id }}">Codi</label><input class="form-control form-control-sm" id="codi-{{ $modul->id }}" name="codigo" form="actualitzar-{{ $modul->id }}" inputmode="numeric" maxlength="30" required value="{{ $modul->codigo }}"></td>
                    <td><label class="visually-hidden" for="cas-{{ $modul->id }}">Cicle en castellà</label><input class="form-control form-control-sm" id="cas-{{ $modul->id }}" name="cicle_cas" form="actualitzar-{{ $modul->id }}" maxlength="255" required value="{{ $modul->cicle_cas }}"></td>
                    <td><label class="visually-hidden" for="val-{{ $modul->id }}">Cicle en valencià</label><input class="form-control form-control-sm" id="val-{{ $modul->id }}" name="cicle_val" form="actualitzar-{{ $modul->id }}" maxlength="255" required value="{{ $modul->cicle_val }}"></td>
                    <td><label class="visually-hidden" for="nivell-{{ $modul->id }}">Nivell</label><select class="form-select form-select-sm" id="nivell-{{ $modul->id }}" name="nivell" form="actualitzar-{{ $modul->id }}"><option value="GM" @selected($modul->nivell === 'GM')>GM</option><option value="GS" @selected($modul->nivell === 'GS')>GS</option></select></td>
                    <td class="text-nowrap">
                        <form id="actualitzar-{{ $modul->id }}" method="POST" action="{{ route('convalidacions.direction.fol-loe.update', $modul) }}">@csrf @method('PUT')<button class="btn btn-sm btn-outline-primary" type="submit">Guardar</button></form>
                        <form method="POST" action="{{ route('convalidacions.direction.fol-loe.destroy', $modul) }}" class="d-inline" onsubmit="return confirm('Vols eliminar este mòdul FOL LOE del catàleg?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No hi ha mòduls FOL LOE configurats.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
