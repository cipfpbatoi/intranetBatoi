@extends('layouts.intranet')

@section('titulo', 'Avaluacions d\'ITACA per a convalidacions')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="mb-1">Avaluacions d'ITACA per a convalidacions</h1>
            <p class="mb-0">Afig i gestiona les avaluacions anuals d'ITACA que permeten comprovar els mòduls aprovats al centre.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('convalidacions.direction.index') }}">Tornar a convalidacions</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><strong>Afegir una avaluació d'ITACA</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('convalidacions.direction.xml.store') }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label" for="xml-nou">Fitxer de l'avaluació d'ITACA</label>
                <input class="form-control mb-2" id="xml-nou" type="file" name="xml" accept=".xml" required>
                <div class="form-text mb-3">Format XML. Mida màxima: {{ round($maxXmlKb / 1024, 1) }} MB. El contingut no serà descarregable des de l'aplicació.</div>
                <button class="btn btn-primary" type="submit">Afegir avaluació</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Avaluacions disponibles</strong></div>
        <div class="card-body p-0">
            @if ($fitxers === [])
                <p class="text-muted m-3">Encara no hi ha cap avaluació d'ITACA incorporada.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr><th>Any</th><th>Fitxer</th><th>Mida</th><th>Actualitzat</th><th>Accions</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($fitxers as $fitxer)
                            <tr>
                                <td>{{ $fitxer['any'] ?: 'Sense any' }}</td>
                                <td>{{ $fitxer['nom'] }}</td>
                                <td>{{ number_format($fitxer['mida'] / 1024, 1, ',', '.') }} KiB</td>
                                <td>{{ date('d/m/Y H:i', $fitxer['modificat']) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('convalidacions.direction.xml.destroy', $fitxer['id']) }}" onsubmit="return confirm('Vols eliminar esta font de les consultes futures?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                    </form>
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
