@extends('layouts.intranet')

@section('titulo', 'Correspondències d’anglés')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="mb-1">Correspondències d’anglés</h1>
            <p class="mb-0 text-muted">Cicles d’anglés amb almenys 5 hores setmanals i el cicle formatiu al qual pertanyen.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('convalidacions.direction.angles.create') }}">Afegir correspondència</a>
            <a class="btn btn-secondary" href="{{ route('convalidacions.direction.index') }}">Tornar a convalidacions</a>
        </div>
    </div>

    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><strong>Importar CSV</strong></div>
        <div class="card-body">
            <p class="small">La importació reemplaça les correspondències dels cicles contenidors presents al fitxer. Els altres cicles no es modifiquen. Màxim 2 MB.</p>
            <form method="POST" action="{{ route('convalidacions.direction.angles.import') }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label" for="csv-correspondencies">Fitxer CSV</label>
                <input class="form-control mb-2" id="csv-correspondencies" name="csv" type="file" accept=".csv,.txt" required>
                <div class="form-text mb-3">Capçalera: <code>codi_cicle_angles,nom_cicle_angles_val,nom_cicle_angles_cas,codi_cicle_contenidor,nom_cicle_contenidor_val,nom_cicle_contenidor_cas,es_grau_superior</code></div>
                <button class="btn btn-outline-primary" type="submit">Validar i importar</button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">Cicle d’anglés</th>
                    <th scope="col">Cicle contenidor</th>
                    <th scope="col">Nivell</th>
                    <th scope="col">Accions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($correspondencies as $correspondencia)
                    <tr>
                        <td><code>{{ $correspondencia->codi_cicle_angles }}</code><div>{{ $correspondencia->nom_cicle_angles_val }}</div><div class="small text-muted">{{ $correspondencia->nom_cicle_angles_cas }}</div></td>
                        <td><code>{{ $correspondencia->codi_cicle_contenidor }}</code><div>{{ $correspondencia->nom_cicle_contenidor_val }}</div><div class="small text-muted">{{ $correspondencia->nom_cicle_contenidor_cas }}</div></td>
                        <td>{{ $correspondencia->es_grau_superior ? 'Grau superior' : 'No és grau superior' }}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('convalidacions.direction.angles.edit', $correspondencia) }}">Editar</a>
                            <form class="d-inline" method="POST" action="{{ route('convalidacions.direction.angles.destroy', $correspondencia) }}" onsubmit="return confirm('Vols eliminar esta correspondència?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Encara no hi ha correspondències. Importa un CSV o afig-ne una manualment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
