<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmació de dades d’empresa</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background: #f4f6f9; color: #263238; }
        .confirmation-shell { max-width: 980px; margin: 32px auto; padding: 0 16px 40px; }
        .confirmation-header { border-top: 5px solid #0d6efd; }
        .section-card { margin-top: 20px; }
        .required::after { content: ' *'; color: #c62828; }
        .readonly-id { background: #eef1f4 !important; }
    </style>
</head>
<body>
<main class="confirmation-shell">
    <div class="card shadow-sm confirmation-header">
        <div class="card-body p-4">
            <h1 class="h3 mb-2">Confirmació de dades de formació en empresa</h1>
            <p class="mb-0"><strong>{{ $confirmation->empresa->nombre }}</strong></p>
            <small class="text-muted">Sol·licitud enviada el {{ optional($confirmation->sent_at)->format('d/m/Y') }}</small>
        </div>
    </div>

    @if ($confirmation->confirmed_at)
        <div class="alert alert-success mt-4">
            <h2 class="h5">Dades confirmades correctament</h2>
            <p class="mb-0">La confirmació es va registrar el {{ $confirmation->confirmed_at->format('d/m/Y H:i') }}. No cal fer cap altra acció.</p>
        </div>
    @elseif (!$confirmation->expires_at->isFuture())
        <div class="alert alert-warning mt-4">
            <h2 class="h5">L’enllaç ha caducat</h2>
            <p class="mb-0">Contacteu amb el tutor o tutora del CIPFP Batoi perquè vos envie una sol·licitud nova.</p>
        </div>
    @else
        @if ($errors->any())
            <div class="alert alert-danger mt-4">
                <strong>Reviseu els camps indicats:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('empresa.confirmacio.update', ['token' => $token]) }}">
            @csrf
            <section class="card shadow-sm section-card">
                <div class="card-body p-4">
                    <h2 class="h4">1. Dades de l’empresa</h2>
                    <p class="text-muted">Reviseu les dades existents i corregiu-les si és necessari.</p>
                    <div class="row g-3">
                        <div class="col-md-8"><label class="form-label required">Nom</label><input class="form-control" name="empresa[nombre]" value="{{ old('empresa.nombre', $confirmation->empresa->nombre) }}" required></div>
                        <div class="col-md-4"><label class="form-label required">CIF/NIF</label><input class="form-control" name="empresa[cif]" value="{{ old('empresa.cif', $confirmation->empresa->cif) }}" required></div>
                        <div class="col-md-8"><label class="form-label required">Adreça</label><input class="form-control" name="empresa[direccion]" value="{{ old('empresa.direccion', $confirmation->empresa->direccion) }}" required></div>
                        <div class="col-md-4"><label class="form-label required">Localitat</label><input class="form-control" name="empresa[localidad]" value="{{ old('empresa.localidad', $confirmation->empresa->localidad) }}" required></div>
                        <div class="col-md-6"><label class="form-label required">Correu electrònic</label><input type="email" class="form-control" name="empresa[email]" value="{{ old('empresa.email', $confirmation->empresa->email) }}" required></div>
                        <div class="col-md-6"><label class="form-label required">Telèfon</label><input class="form-control" name="empresa[telefono]" value="{{ old('empresa.telefono', $confirmation->empresa->telefono) }}" required></div>
                    </div>
                </div>
            </section>

            <section class="card shadow-sm section-card">
                <div class="card-body p-4">
                    <h2 class="h4">2. Gerent o representant legal</h2>
                    <div class="row g-3">
                        <div class="col-md-8"><label class="form-label required">Nom complet</label><input class="form-control" name="empresa[gerente]" value="{{ old('empresa.gerente', $confirmation->empresa->gerente) }}" required></div>
                        <div class="col-md-4"><label class="form-label required">NIF</label><input class="form-control text-uppercase" name="empresa[nif_gerente]" value="{{ old('empresa.nif_gerente', $confirmation->empresa->nif_gerente) }}" required></div>
                    </div>
                </div>
            </section>

            @foreach ($centers as $center)
                @php($centerId = $center->id)
                @php($centerCollaborations = $collaborations->where('idCentro', $centerId))
                <section class="card shadow-sm section-card">
                    <div class="card-body p-4">
                        <h2 class="h4">3. Centre de treball</h2>
                        <h3 class="h5 mt-3">{{ $center->nombre }}</h3>
                        <div class="row g-3">
                            <div class="col-md-12"><label class="form-label required">Nom del centre</label><input class="form-control" name="centers[{{ $centerId }}][nombre]" value="{{ old("centers.$centerId.nombre", $center->nombre) }}" required></div>
                            <div class="col-md-6"><label class="form-label">Correu</label><input type="email" class="form-control" name="centers[{{ $centerId }}][email]" value="{{ old("centers.$centerId.email", $center->email) }}"></div>
                            <div class="col-md-6"><label class="form-label">Telèfon</label><input class="form-control" name="centers[{{ $centerId }}][telefono]" value="{{ old("centers.$centerId.telefono", $center->telefono) }}"></div>
                            <div class="col-md-8"><label class="form-label required">Adreça</label><input class="form-control" name="centers[{{ $centerId }}][direccion]" value="{{ old("centers.$centerId.direccion", $center->direccion) }}" required></div>
                            <div class="col-md-4"><label class="form-label required">Localitat</label><input class="form-control" name="centers[{{ $centerId }}][localidad]" value="{{ old("centers.$centerId.localidad", $center->localidad) }}" required></div>
                            <div class="col-md-12"><label class="form-label">Horari de pràctiques</label><input class="form-control" name="centers[{{ $centerId }}][horarios]" value="{{ old("centers.$centerId.horarios", $center->horarios) }}"></div>
                        </div>

                        <h3 class="h5 mt-4">Formacions</h3>
                        @forelse ($centerCollaborations as $collaboration)
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="col-{{ $collaboration->id }}" name="confirmed_colaborations[]" value="{{ $collaboration->id }}" {{ in_array($collaboration->id, old('confirmed_colaborations', $confirmation->colaboracion_ids)) ? 'checked' : '' }} required>
                                <label class="form-check-label" for="col-{{ $collaboration->id }}">Confirme la formació: <strong>{{ $collaboration->Ciclo->literal }}</strong></label>
                            </div>
                        @empty
                            <p class="text-muted">No hi ha formacions registrades en este centre.</p>
                        @endforelse

                        <h3 class="h5 mt-4">Instructors del centre</h3>
                        @forelse ($center->instructores as $instructor)
                            @php($instructorKey = $instructor->dni)
                            @php($isPrimaryCenter = (int) collect($instructor->confirmation_center_ids)->first() === (int) $centerId)
                            <div class="border rounded p-3 mb-3">
                                @if ($isPrimaryCenter)
                                    <div class="form-check mb-3">
                                        <input type="radio" class="form-check-input" id="coordinator-{{ $instructorKey }}" name="coordinator_dni" value="{{ $instructor->dni }}" {{ old('coordinator_dni', $instructor->coordinador ? $instructor->dni : '') === $instructor->dni ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="coordinator-{{ $instructorKey }}"><strong>Designar com a coordinador/a de l’empresa</strong></label>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3"><label class="form-label">DNI/NIE (no editable)</label><input readonly class="form-control readonly-id" name="instructors[existing][{{ $instructorKey }}][dni]" value="{{ $instructor->dni }}"></div>
                                        <div class="col-md-4"><label class="form-label required">Nom</label><input class="form-control" name="instructors[existing][{{ $instructorKey }}][name]" value="{{ old("instructors.existing.$instructorKey.name", $instructor->name) }}" required></div>
                                        <div class="col-md-5"><label class="form-label required">Cognoms</label><input class="form-control" name="instructors[existing][{{ $instructorKey }}][surnames]" value="{{ old("instructors.existing.$instructorKey.surnames", $instructor->surnames) }}" required></div>
                                        <div class="col-md-7"><label class="form-label required">Correu</label><input type="email" class="form-control" name="instructors[existing][{{ $instructorKey }}][email]" value="{{ old("instructors.existing.$instructorKey.email", $instructor->email) }}" required></div>
                                        <div class="col-md-5"><label class="form-label">Telèfon</label><input class="form-control" name="instructors[existing][{{ $instructorKey }}][telefono]" value="{{ old("instructors.existing.$instructorKey.telefono", $instructor->telefono) }}"></div>
                                    </div>
                                    @if (count($instructor->confirmation_center_names) > 1)
                                        <p class="small text-muted mt-3 mb-0">També està vinculat a: {{ implode(', ', array_values(array_diff($instructor->confirmation_center_names, [$center->nombre]))) }}</p>
                                    @endif
                                @else
                                    <p class="mb-1"><strong>{{ $instructor->nombre }}</strong> · {{ $instructor->dni }}</p>
                                    <p class="small text-muted mb-0">Les dades personals s’editen en el primer centre on apareix.</p>
                                @endif

                                <div class="form-check mt-3">
                                    <input type="checkbox" class="form-check-input" id="remove-{{ $centerId }}-{{ $instructorKey }}" name="instructor_removals[{{ $centerId }}][]" value="{{ $instructor->dni }}" {{ in_array($instructor->dni, old("instructor_removals.$centerId", []), true) ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger" for="remove-{{ $centerId }}-{{ $instructorKey }}">Este instructor ja no està vinculat a este centre</label>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No hi ha instructors registrats en este centre.</p>
                        @endforelse
                    </div>
                </section>
            @endforeach

            <section class="card shadow-sm section-card">
                <div class="card-body p-4">
                    <h2 class="h4">4. Afegir un instructor nou <small class="text-muted">(opcional)</small></h2>
                    <p class="text-muted">Seleccioneu exactament un coordinador o coordinadora entre els instructors que continuen vinculats o el nou instructor.</p>
                    <div class="border rounded p-3">
                        <div class="form-check mb-3">
                            <input type="radio" class="form-check-input" id="coordinator-new" name="coordinator_dni" value="__new__" {{ old('coordinator_dni') === '__new__' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="coordinator-new"><strong>El nou instructor serà el coordinador/a</strong></label>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">DNI/NIE</label><input class="form-control text-uppercase" name="instructors[new][dni]" value="{{ old('instructors.new.dni') }}"></div>
                            <div class="col-md-4"><label class="form-label">Nom</label><input class="form-control" name="instructors[new][name]" value="{{ old('instructors.new.name') }}"></div>
                            <div class="col-md-5"><label class="form-label">Cognoms</label><input class="form-control" name="instructors[new][surnames]" value="{{ old('instructors.new.surnames') }}"></div>
                            <div class="col-md-7"><label class="form-label">Correu</label><input type="email" class="form-control" name="instructors[new][email]" value="{{ old('instructors.new.email') }}"></div>
                            <div class="col-md-5"><label class="form-label">Telèfon</label><input class="form-control" name="instructors[new][telefono]" value="{{ old('instructors.new.telefono') }}"></div>
                            <div class="col-12">
                                <label class="form-label">Centre(s) del nou instructor</label>
                                @foreach ($centers as $center)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="new-center-{{ $center->id }}" name="instructors[new][center_ids][]" value="{{ $center->id }}" {{ in_array($center->id, old('instructors.new.center_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new-center-{{ $center->id }}">{{ $center->nombre }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="card shadow-sm section-card">
                <div class="card-body p-4 d-md-flex justify-content-between align-items-center">
                    <p class="mb-md-0">En enviar, confirmeu que les dades anteriors són correctes.</p>
                    <button type="submit" class="btn btn-primary btn-lg">Enviar confirmació</button>
                </div>
            </div>
        </form>
    @endif
</main>
</body>
</html>
