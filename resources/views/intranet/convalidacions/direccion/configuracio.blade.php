@extends('layouts.intranet')

@section('titulo', 'Configuració de convalidacions')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="mb-1">Configuració de convalidacions</h1>
            <p class="text-muted mb-0">Gestiona les dades i criteris que s’utilitzen en el procés.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('convalidacions.direction.index') }}">Tornar a les sol·licituds</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <section class="card h-100">
                <div class="card-body">
                    <h2 class="h5">Obertura de sol·licituds</h2>
                    <p class="text-muted">Controla qui pot presentar una nova sol·licitud de convalidació.</p>
                    <form method="POST" action="{{ route('convalidacions.direction.new-requests-access') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="new_requests_enabled" value="0">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="new-requests-enabled" name="new_requests_enabled" value="1" @checked($newRequestsEnabled)>
                            <label class="form-check-label" for="new-requests-enabled">Permetre noves sol·licituds</label>
                        </div>
                        <input type="hidden" name="july_enrolments_blocked" value="0">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="july-enrolments-blocked" name="july_enrolments_blocked" value="1" @checked($julyEnrolmentsBlocked)>
                            <label class="form-check-label" for="july-enrolments-blocked">Bloquejar noves sol·licituds per a matrícules de juliol</label>
                        </div>
                        <button class="btn btn-outline-primary" type="submit">Guardar obertura</button>
                    </form>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <section class="card h-100">
                <div class="card-body">
                    <h2 class="h5">Regles automàtiques</h2>
                    <p class="text-muted">Consulta i actualitza les regles que determinen quines peticions poden resoldre’s automàticament.</p>
                    <a class="btn btn-outline-primary" href="{{ route('convalidacions.direction.rules.index') }}">Configurar regles</a>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <section class="card h-100">
                <div class="card-body">
                    <h2 class="h5">Mòduls FOL LOE</h2>
                    <p class="text-muted">Mantín els codis de FOL LOE que s’utilitzen per identificar estudis d’origen.</p>
                    <a class="btn btn-outline-primary" href="{{ route('convalidacions.direction.fol-loe.index') }}">Gestionar catàleg FOL LOE</a>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <section class="card h-100">
                <div class="card-body">
                    <h2 class="h5">Correspondències d’anglés</h2>
                    <p class="text-muted">Gestiona els cicles d’anglés i les correspondències amb els cicles contenidors.</p>
                    <a class="btn btn-outline-primary" href="{{ route('convalidacions.direction.angles.index') }}">Configurar correspondències</a>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <section class="card h-100">
                <div class="card-body">
                    <h2 class="h5">Avaluacions d’ITACA</h2>
                    <p class="text-muted">Importa i administra els fitxers d’avaluacions necessaris per consultar els mòduls superats.</p>
                    <a class="btn btn-outline-primary" href="{{ route('convalidacions.direction.xml.index') }}">Gestionar avaluacions</a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
