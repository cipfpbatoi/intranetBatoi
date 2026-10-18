@once
    @push('styles')
        <style>
            .colaboracion-company-list {
                display: flex;
                flex-direction: column;
                gap: 18px;
            }

            .colaboracion-company-block {
                overflow: hidden;
                border: 1px solid #cfd8e3;
                border-left: 5px solid #337ab7;
                border-radius: 6px;
                background: #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, .06);
            }

            .colaboracion-company-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 12px 16px;
                border-bottom: 1px solid #dce4ec;
                background: #f2f7fb;
            }

            .colaboracion-company-name {
                margin: 0;
                color: #144a75;
                font-size: 1.1rem;
                font-weight: 700;
            }

            .colaboracion-company-collaborations {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 10px;
                padding: 14px 16px 16px;
            }

            .colaboracion-company-item {
                display: block;
                min-width: 0;
                padding: 10px 12px;
                border: 1px solid #e1e6eb;
                border-radius: 4px;
                background: #fbfbfc;
            }

            .colaboracion-company-item:hover,
            .colaboracion-company-item:focus {
                border-color: #78aee8;
                background: #f5f9fd;
            }

            @media (min-width: 992px) {
                .colaboracion-company-collaborations {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (min-width: 1700px) {
                .colaboracion-company-collaborations {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }
        </style>
    @endpush
@endonce

@php
    $today = \Illuminate\Support\Carbon::today();
    $currentCourseStart = $today->month >= 9
        ? $today->copy()->startOfYear()->month(9)->day(1)
        : $today->copy()->subYear()->startOfYear()->month(9)->day(1);
    $elementos = $panel->getElementos($pestana);
    $townGroups = $elementos
        ->sortBy([
            ['localidad', 'asc'],
            ['empresa', 'asc'],
        ])
        ->groupBy(fn ($elemento) => $elemento->localidad ?: 'Desconeguda');
    $townOptions = $townGroups->keys()->values();
    $dashboardSummary = [
        'total' => $elementos->count(),
        'pendents' => $elementos->where('estado', 1)->count(),
        'colaboren' => $elementos->where('estado', 2)->count(),
        'noColaboren' => $elementos->where('estado', 3)->count(),
        'senseClassificar' => $elementos
            ->reject(static fn ($elemento) => in_array((int) ($elemento->estado ?? 0), [1, 2, 3], true))
            ->count(),
    ];
    $byTutor = $elementos
        ->groupBy(static fn ($elemento) => $elemento->profesor ?: 'Sense tutor assignat')
        ->map(static function ($items, $label) use ($currentCourseStart) {
            return [
                'label' => $label,
                'total' => $items->count(),
                'senseContacteCurs' => $items->filter(static function ($elemento) use ($currentCourseStart) {
                    $ultima = $elemento->ultimaActividad?->created_at;
                    return $ultima === null || \Illuminate\Support\Carbon::parse($ultima)->lt($currentCourseStart);
                })->count(),
                'preparades' => $items->where('estatPreparacioKey', 'preparada')->count(),
                'ambAlumnat' => $items->filter(static fn ($elemento) => (int) ($elemento->fctsAssociadesCount ?? 0) > 0)->count(),
            ];
        })
        ->sortByDesc('total');
    $byCycle = $elementos
        ->groupBy(static fn ($elemento) => optional($elemento->Ciclo)->literal ?? ('Cicle ' . ($elemento->idCiclo ?? '')))
        ->map(static function ($items, $label) use ($currentCourseStart) {
            return [
                'label' => $label,
                'total' => $items->count(),
                'senseContacteCurs' => $items->filter(static function ($elemento) use ($currentCourseStart) {
                    $ultima = $elemento->ultimaActividad?->created_at;
                    return $ultima === null || \Illuminate\Support\Carbon::parse($ultima)->lt($currentCourseStart);
                })->count(),
                'preparades' => $items->where('estatPreparacioKey', 'preparada')->count(),
                'ambAlumnat' => $items->filter(static fn ($elemento) => (int) ($elemento->fctsAssociadesCount ?? 0) > 0)->count(),
            ];
        })
        ->sortBy('label');

    $cyclePalette = [
        ['background' => '#e8f3ff', 'border' => '#78aee8', 'text' => '#144a75'],
        ['background' => '#eef7e8', 'border' => '#8dbb61', 'text' => '#35551a'],
        ['background' => '#fff2e8', 'border' => '#d59a60', 'text' => '#7a4314'],
        ['background' => '#f3ecff', 'border' => '#9a7ad9', 'text' => '#4c2b8a'],
        ['background' => '#e8f7f5', 'border' => '#58b4a4', 'text' => '#1f5e55'],
        ['background' => '#fff0f4', 'border' => '#d97897', 'text' => '#7c1f42'],
    ];

    $cycleStyle = static function ($elemento) use ($cyclePalette): string {
        $seed = (string) (optional($elemento->Ciclo)->literal ?? $elemento->idCiclo ?? $elemento->id ?? '0');
        $index = abs(crc32($seed)) % count($cyclePalette);
        $colors = $cyclePalette[$index];

        return sprintf(
            'background:%s;border:1px solid %s;color:%s;',
            $colors['background'],
            $colors['border'],
            $colors['text']
        );
    };

    $contactStatus = static function ($elemento): array {
        return match ((int) ($elemento->estado ?? 0)) {
            2 => [
                'label' => 'Contactada',
                'class' => 'text-success',
            ],
            3 => [
                'label' => 'No col·labora',
                'class' => 'text-danger',
            ],
            1 => [
                'label' => 'No contactada',
                'class' => 'text-warning',
            ],
            default => [
                'label' => null,
                'class' => 'text-muted',
            ],
        };
    };
@endphp

<div class="x_content">
    <div class="mb-3">
        <a href="{{ route('colaboracion.mias') }}" class="btn btn-primary btn-sm">
            <em class="fa fa-user"></em> Vore misColaboraciones
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-2 col-sm-4 col-xs-6 mb-3">
            <div class="rounded border bg-white p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                <div class="small text-muted">Total</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $dashboardSummary['total'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 mb-3">
            <div class="rounded border bg-white p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                <div class="small text-muted">Pendents</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #8a6d3b;">{{ $dashboardSummary['pendents'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 mb-3">
            <div class="rounded border bg-white p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                <div class="small text-muted">Col·laboren</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #1f7a1f;">{{ $dashboardSummary['colaboren'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 mb-3">
            <div class="rounded border bg-white p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                <div class="small text-muted">No col·laboren</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #a94442;">{{ $dashboardSummary['noColaboren'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 mb-3">
            <div class="rounded border bg-white p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                <div class="small text-muted">Sense classificar</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #144a75;">{{ $dashboardSummary['senseClassificar'] }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 mb-3">
            <div class="rounded border bg-white p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                <div class="small text-muted">Quadratura</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #555;">{{ $dashboardSummary['pendents'] + $dashboardSummary['colaboren'] + $dashboardSummary['noColaboren'] + $dashboardSummary['senseClassificar'] }}</div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="rounded border bg-white p-3 h-100" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                <h4 class="mb-3">Per tutor</h4>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                        <tr>
                            <th>Tutor</th>
                            <th>Total</th>
                            <th>Sense contacte</th>
                            <th>Preparades</th>
                            <th>Amb alumnat</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($byTutor as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ $row['total'] }}</td>
                                <td>{{ $row['senseContacteCurs'] }}</td>
                                <td>{{ $row['preparades'] }}</td>
                                <td>{{ $row['ambAlumnat'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="rounded border bg-white p-3 h-100" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                <h4 class="mb-3">Per cicle</h4>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                        <tr>
                            <th>Cicle</th>
                            <th>Total</th>
                            <th>Sense contacte</th>
                            <th>Preparades</th>
                            <th>Amb alumnat</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($byCycle as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ $row['total'] }}</td>
                                <td>{{ $row['senseContacteCurs'] }}</td>
                                <td>{{ $row['preparades'] }}</td>
                                <td>{{ $row['ambAlumnat'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="colaboracion-town-filter" class="form-label fw-semibold">Filtrar per poble</label>
        <input id="colaboracion-town-filter"
               class="form-control"
               list="colaboracion-town-options"
               type="search"
               placeholder="Escriu part del poble">
        <datalist id="colaboracion-town-options">
            @foreach ($townOptions as $townOption)
                <option value="{{ $townOption }}"></option>
            @endforeach
        </datalist>
    </div>

    @forelse ($townGroups as $localidad => $townItems)
        @php
            $centers = $townItems->groupBy('empresa');
        @endphp

        <div class="mb-4 colaboracion-town-group" data-town="{{ $localidad }}">
            <div class="d-flex align-items-center gap-2 mb-3 pb-1" style="border-bottom: 1px solid #e6e9ed;">
                <span class="badge bg-secondary-subtle text-dark border">
                    <em class="fa fa-map-marker"></em> {{ $localidad }}
                </span>
                <small class="text-muted">
                    {{ $centers->count() }} {{ $centers->count() === 1 ? 'empresa' : 'empreses' }}
                </small>
            </div>

            <div class="colaboracion-company-list">
                @foreach ($centers as $centerName => $centerItems)
                    <section class="colaboracion-company-block" data-company="{{ $centerName }}">
                        <header class="colaboracion-company-header">
                            <div>
                                <div class="small text-muted text-uppercase">Empresa</div>
                                <h4 class="colaboracion-company-name">
                                    <em class="fa fa-building" aria-hidden="true"></em>
                                    {{ $centerName }}
                                </h4>
                            </div>
                            <span class="badge bg-primary">
                                {{ $centerItems->count() }}
                                {{ $centerItems->count() === 1 ? 'col·laboració' : 'col·laboracions' }}
                            </span>
                        </header>

                        <div class="colaboracion-company-collaborations">
                            @foreach ($centerItems as $elemento)
                                @php
                                    $status = $contactStatus($elemento);
                                    $responsable = $elemento->profesor ?: 'Sense assignar';
                                @endphp

                                <a href="{{ route('colaboracion.show', ['colaboracion' => $elemento->id]) }}"
                                   class="js-colaboracion-show colaboracion-company-item text-decoration-none text-reset"
                                   data-show-title="Detall col·laboració">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="d-flex flex-wrap gap-1">
                                            <span class="badge"
                                                  style="{{ $cycleStyle($elemento) }}">
                                                {{ optional($elemento->Ciclo)->literal ?? ('Cicle ' . ($elemento->idCiclo ?? '')) }}
                                            </span>
                                        </div>

                                        @if ($status['label'])
                                            <span class="small {{ $status['class'] }}">
                                                {{ $status['label'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="small text-muted mt-2">
                                        <em class="fa fa-user" aria-hidden="true"></em>
                                        {{ !empty($elemento->tutor) ? $responsable : 'Sense assignar' }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    @empty
        <div class="alert alert-info mb-0">
            No hi ha col·laboracions disponibles per al departament.
        </div>
    @endforelse
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var filter = document.getElementById('colaboracion-town-filter');
            if (!filter) {
                return;
            }

            var groups = Array.from(document.querySelectorAll('.colaboracion-town-group'));
            var normalizeTown = function (value) {
                return (value || '')
                    .toString()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toUpperCase()
                    .replace(/[0-9]/g, ' ')
                    .replace(/Y/g, 'I')
                    .replace(/[^A-Z\s]/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();
            };

            var applyTownFilter = function () {
                var selectedTown = normalizeTown(filter.value);

                groups.forEach(function (group) {
                    var groupTown = normalizeTown(group.getAttribute('data-town') || '');
                    group.style.display = (!selectedTown || groupTown.indexOf(selectedTown) !== -1) ? '' : 'none';
                });
            };

            filter.addEventListener('input', applyTownFilter);
            filter.addEventListener('change', applyTownFilter);
        });
    </script>
@endpush
