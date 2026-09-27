@extends('layouts.intranet')

@section('titulo', 'Resultats acadèmics per a convalidacions')

@section('content')
<div class="container">
    <h1>Resultats acadèmics per a convalidacions</h1>
    <p>Gestiona les exportacions anuals que permeten comprovar els mòduls aprovats al centre.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><strong>Incorporar XML anual</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('convalidacions.xml.store') }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label" for="xml-nou">Fitxer XML d'avaluació</label>
                <input class="form-control mb-2" id="xml-nou" type="file" name="xml" accept=".xml" required>
                <div class="form-text mb-3">Mida màxima: {{ round($maxXmlKb / 1024, 1) }} MB. El contingut no serà descarregable des de l'aplicació.</div>
                <button class="btn btn-primary" type="submit">Incorporar XML</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Fonts disponibles</strong></div>
        <div class="card-body p-0">
            @if ($fitxers === [])
                <p class="text-muted m-3">Encara no hi ha cap XML incorporat.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr><th>Fitxer</th><th>Mida</th><th>Actualitzat</th><th>Accions</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($fitxers as $fitxer)
                            <tr>
                                <td>{{ $fitxer['nom'] }}</td>
                                <td>{{ number_format($fitxer['mida'] / 1024, 1, ',', '.') }} KiB</td>
                                <td>{{ date('d/m/Y H:i', $fitxer['modificat']) }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <form class="d-flex gap-2" method="POST" action="{{ route('convalidacions.xml.replace', $fitxer['id']) }}" enctype="multipart/form-data">
                                            @csrf @method('PUT')
                                            <input class="form-control form-control-sm" type="file" name="xml" accept=".xml" aria-label="Nou XML per a {{ $fitxer['nom'] }}" required>
                                            <button class="btn btn-sm btn-outline-primary" type="submit">Substituir</button>
                                        </form>
                                        <form method="POST" action="{{ route('convalidacions.xml.destroy', $fitxer['id']) }}" onsubmit="return confirm('Vols eliminar esta font de les consultes futures?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                        </form>
                                    </div>
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
