@once
    <style data-colaboracion-department-styles>
        .colaboracion-filter-panel {
            margin-bottom: 16px;
            padding: 12px;
            border: 1px solid #d9e1e8;
            border-radius: 6px;
            background: #f8fafc;
        }

        .colaboracion-filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(165px, 1fr));
            gap: 10px;
            align-items: end;
        }

        .colaboracion-filter-field label {
            display: block;
            margin-bottom: 4px;
            color: #34495e;
            font-size: .82rem;
            font-weight: 700;
        }

        .colaboracion-filter-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .colaboracion-filter-actions .btn {
            margin: 0;
        }

        .colaboracion-result-summary {
            margin: 10px 0 0;
            color: #5d6d7e;
            font-size: .85rem;
        }

        .colaboracion-company-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
            width: 100%;
        }

        .colaboracion-company-card {
            min-width: 0;
            overflow: hidden;
            border: 2px solid #ccd8e4;
            border-radius: 7px;
            box-shadow: 0 2px 6px rgba(20, 50, 80, .08);
        }

        .colaboracion-company-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            padding: 11px 12px 9px;
            border-bottom: 1px solid rgba(80, 105, 130, .16);
        }

        .colaboracion-company-name {
            overflow: hidden;
            margin: 0;
            color: #174b73;
            font-size: 1rem;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .colaboracion-company-town {
            margin-top: 3px;
            color: #647587;
            font-size: .8rem;
        }

        .colaboracion-cycle-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 9px;
        }

        .colaboracion-cycle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            min-width: 0;
            padding: 8px 9px;
            border: 1px solid rgba(90, 110, 130, .18);
            border-radius: 5px;
            background: rgba(255, 255, 255, .72);
        }

        .colaboracion-cycle-row:hover,
        .colaboracion-cycle-row:focus {
            border-color: #6da8db;
            background: #fff;
            box-shadow: 0 1px 4px rgba(20, 74, 117, .12);
        }

        .colaboracion-cycle-main,
        .colaboracion-cycle-status {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 5px;
            min-width: 0;
        }

        .colaboracion-cycle-status {
            justify-content: flex-end;
        }

        .colaboracion-cycle-badge {
            font-weight: 700;
        }

        .colaboracion-professor {
            overflow: hidden;
            color: #586979;
            font-size: .8rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (min-width: 768px) {
            .colaboracion-company-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 992px) {
            .colaboracion-company-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>
@endonce

@php
    $elementos = $panel->getElementos($pestana)
        ->sortBy([
            ['empresa', 'asc'],
            ['idCiclo', 'asc'],
        ])
        ->values();
    $empreses = $elementos
        ->groupBy(static fn ($elemento): string => (string) ($elemento->idCentro ?? ('colaboracio-' . $elemento->id)))
        ->sortBy(static fn ($items): string => (string) ($items->first()->empresa ?? ''));
    $townOptions = $elementos
        ->pluck('localidad')
        ->filter()
        ->unique()
        ->sort()
        ->values();
    $cycleLabel = static function ($elemento): string {
        $cycle = optional($elemento->Ciclo);

        return (string) (
            $cycle->ciclo
            ?? $cycle->acronim
            ?? $cycle->literal
            ?? ('Cicle ' . ($elemento->idCiclo ?? ''))
        );
    };
    $cycleOptions = $elementos
        ->map(static fn ($elemento): array => [
            'id' => (string) ($elemento->idCiclo ?? ''),
            'label' => $cycleLabel($elemento),
        ])
        ->filter(static fn (array $cycle): bool => $cycle['id'] !== '')
        ->unique('id')
        ->sortBy('label')
        ->values();
    $status = static function ($elemento): array {
        return match ((int) ($elemento->estado ?? 0)) {
            1 => ['label' => 'Contactada', 'class' => 'bg-info text-dark'],
            2 => ['label' => 'Col·labora', 'class' => 'bg-success'],
            3 => ['label' => 'No col·labora', 'class' => 'bg-danger'],
            default => ['label' => 'Sense contacte', 'class' => 'bg-secondary'],
        };
    };
    $cyclePalette = [
        ['background' => '#e8f3ff', 'border' => '#78aee8', 'text' => '#144a75'],
        ['background' => '#eef7e8', 'border' => '#8dbb61', 'text' => '#35551a'],
        ['background' => '#fff2e8', 'border' => '#d59a60', 'text' => '#7a4314'],
        ['background' => '#f3ecff', 'border' => '#9a7ad9', 'text' => '#4c2b8a'],
        ['background' => '#e8f7f5', 'border' => '#58b4a4', 'text' => '#1f5e55'],
        ['background' => '#fff0f4', 'border' => '#d97897', 'text' => '#7c1f42'],
    ];
    $companyPalette = [
        ['start' => '#dceeff', 'border' => '#90bde6'],
        ['start' => '#e3f3da', 'border' => '#a5ca8e'],
        ['start' => '#ffead6', 'border' => '#dfb184'],
        ['start' => '#eee3ff', 'border' => '#b6a0dc'],
        ['start' => '#ddf2ee', 'border' => '#8fc8bd'],
        ['start' => '#fbe2ea', 'border' => '#d8a1b3'],
    ];
    $paletteColor = static function (string $seed, array $palette): array {
        return $palette[abs(crc32($seed)) % count($palette)];
    };
    $cycleStyle = static function ($elemento) use ($cyclePalette, $paletteColor): string {
        $seed = (string) (optional($elemento->Ciclo)->literal ?? $elemento->idCiclo ?? $elemento->id ?? '0');
        $colors = $paletteColor($seed, $cyclePalette);

        return sprintf(
            'background:%s;border:1px solid %s;color:%s;',
            $colors['background'],
            $colors['border'],
            $colors['text']
        );
    };
    $companyStyle = static function (string $companyKey) use ($companyPalette, $paletteColor): string {
        $colors = $paletteColor($companyKey, $companyPalette);

        return sprintf(
            'background:linear-gradient(135deg,%s 0%%,#ffffff 88%%);border-color:%s;',
            $colors['start'],
            $colors['border']
        );
    };
@endphp

<div class="x_content" id="colaboracion-department-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <p class="small text-muted mb-0">
            Filtra les col·laboracions i obri el cicle per consultar-ne el detall.
        </p>
        <a href="{{ route('colaboracion.mias') }}" class="btn btn-default btn-sm">
            <em class="fa fa-user" aria-hidden="true"></em> Les meues col·laboracions
        </a>
    </div>

    <div class="colaboracion-filter-panel" aria-label="Filtres de col·laboracions">
        <div class="colaboracion-filter-grid">
            <div class="colaboracion-filter-field">
                <label for="colaboracion-town-filter">Població</label>
                <select id="colaboracion-town-filter" class="form-control input-sm">
                    <option value="">Totes les poblacions</option>
                    @foreach ($townOptions as $townOption)
                        <option value="{{ $townOption }}">{{ $townOption }}</option>
                    @endforeach
                </select>
            </div>

            <div class="colaboracion-filter-field">
                <label for="colaboracion-cycle-filter">Cicle</label>
                <select id="colaboracion-cycle-filter" class="form-control input-sm">
                    <option value="">Tots els cicles</option>
                    @foreach ($cycleOptions as $cycleOption)
                        <option value="{{ $cycleOption['id'] }}">{{ $cycleOption['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="colaboracion-filter-field">
                <label for="colaboracion-state-filter">Estat</label>
                <select id="colaboracion-state-filter" class="form-control input-sm">
                    <option value="">Tots els estats</option>
                    <option value="no-contact">Sense contacte</option>
                    <option value="contacted">Contactada</option>
                    <option value="collaborates">Col·labora</option>
                </select>
            </div>

            <div class="colaboracion-filter-field">
                <label for="colaboracion-assignment-filter">Assignació</label>
                <select id="colaboracion-assignment-filter" class="form-control input-sm">
                    <option value="">Totes</option>
                    <option value="assigned">Assignades</option>
                    <option value="unassigned">Sense assignar</option>
                </select>
            </div>

            <div class="colaboracion-filter-actions">
                <button type="button"
                        id="colaboracion-documentation-filter"
                        class="btn btn-default btn-sm"
                        aria-pressed="false">
                    <em class="fa fa-file-text-o" aria-hidden="true"></em>
                    Documentació actualitzada
                </button>
                <button type="button" id="colaboracion-clear-filters" class="btn btn-default btn-sm">
                    <em class="fa fa-times" aria-hidden="true"></em>
                    Netejar filtres
                </button>
            </div>
        </div>

        <p class="colaboracion-result-summary" aria-live="polite">
            <strong id="colaboracion-visible-companies">{{ $empreses->count() }}</strong> empreses ·
            <strong id="colaboracion-visible-collaborations">{{ $elementos->count() }}</strong> col·laboracions
        </p>
    </div>

    <div id="colaboracion-company-grid" class="colaboracion-company-grid">
        @foreach ($empreses as $companyKey => $companyItems)
            @php
                $first = $companyItems->first();
                $companyName = (string) ($first->empresa ?? 'Empresa sense nom');
                $town = (string) ($first->localidad ?? 'Desconeguda');
            @endphp
            <article class="colaboracion-company-card"
                     data-company-key="{{ $companyKey }}"
                     style="{{ $companyStyle((string) $companyKey) }}">
                <header class="colaboracion-company-header">
                    <div class="min-w-0">
                        <h4 class="colaboracion-company-name" title="{{ $companyName }}">
                            <em class="fa fa-building" aria-hidden="true"></em>
                            {{ $companyName }}
                        </h4>
                        <div class="colaboracion-company-town">
                            <em class="fa fa-map-marker" aria-hidden="true"></em>
                            {{ $town }}
                        </div>
                    </div>
                    <span class="badge bg-secondary js-company-cycle-count">{{ $companyItems->count() }}</span>
                </header>

                <div class="colaboracion-cycle-list">
                    @foreach ($companyItems as $elemento)
                        @php
                            $cycle = optional($elemento->Ciclo);
                            $cycleName = $cycleLabel($elemento);
                            $cycleTitle = (string) ($cycle->literal ?? $cycleName);
                            $responsable = trim((string) (optional($elemento->Propietario)->shortName ?? ''));
                            $assigned = !empty($elemento->tutor);
                            $cardStatus = $status($elemento);
                        @endphp
                        <a href="{{ route('colaboracion.show', ['colaboracion' => $elemento->id]) }}"
                           class="js-colaboracion-show colaboracion-cycle-row text-decoration-none text-reset"
                           data-show-title="Detall col·laboració"
                           data-town="{{ $elemento->localidad }}"
                           data-cycle="{{ $elemento->idCiclo }}"
                           data-state="{{ (int) ($elemento->estado ?? 0) }}"
                           data-documentation-current="{{ empty($elemento->conveniPendent) ? '1' : '0' }}"
                           data-assigned="{{ $assigned ? '1' : '0' }}">
                            <span class="colaboracion-cycle-main">
                                <span class="badge colaboracion-cycle-badge"
                                      style="{{ $cycleStyle($elemento) }}"
                                      title="{{ $cycleTitle }}">
                                    {{ $cycleName }}
                                </span>
                                @if (!empty($elemento->esCiclePropi))
                                    <span class="badge bg-primary">El meu cicle</span>
                                @endif
                                <span class="colaboracion-professor">
                                    @if ($assigned)
                                        <em class="fa fa-user" aria-hidden="true"></em>
                                        {{ $responsable !== '' ? $responsable : 'Assignada' }}
                                    @else
                                        <span role="img" aria-label="Sense assignar" title="Sense assignar">
                                            <em class="fa fa-user-times text-danger" aria-hidden="true"></em>
                                        </span>
                                    @endif
                                </span>
                            </span>
                            <span class="colaboracion-cycle-status">
                                <span class="badge {{ $cardStatus['class'] }}">{{ $cardStatus['label'] }}</span>
                                @if (!empty($elemento->conveniPendent))
                                    <span class="badge bg-warning text-dark">Documentació antiga</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            </article>
        @endforeach
    </div>

    <div id="colaboracion-department-empty" class="alert alert-info mt-3 mb-0" hidden>
        No hi ha col·laboracions que complisquen tots els filtres seleccionats.
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var panel = document.getElementById('colaboracion-department-panel');
            var grid = document.getElementById('colaboracion-company-grid');
            var townFilter = document.getElementById('colaboracion-town-filter');
            var cycleFilter = document.getElementById('colaboracion-cycle-filter');
            var stateFilter = document.getElementById('colaboracion-state-filter');
            var assignmentFilter = document.getElementById('colaboracion-assignment-filter');
            var documentationFilter = document.getElementById('colaboracion-documentation-filter');
            var clearFilters = document.getElementById('colaboracion-clear-filters');
            var companiesOutput = document.getElementById('colaboracion-visible-companies');
            var collaborationsOutput = document.getElementById('colaboracion-visible-collaborations');
            var emptyState = document.getElementById('colaboracion-department-empty');

            if (!panel || !grid || !townFilter || !cycleFilter || !stateFilter
                || !assignmentFilter || !documentationFilter || !clearFilters
                || !companiesOutput || !collaborationsOutput || !emptyState) {
                return;
            }

            var cards = Array.from(grid.querySelectorAll('.colaboracion-company-card'));
            var documentationOnly = false;

            var matchesState = function (row) {
                var selectedState = stateFilter.value;
                var state = row.getAttribute('data-state') || '0';

                if (selectedState === 'no-contact') {
                    return state === '0';
                }
                if (selectedState === 'contacted') {
                    return state !== '0';
                }
                if (selectedState === 'collaborates') {
                    return state === '2';
                }

                return true;
            };

            var matchesAssignment = function (row) {
                if (assignmentFilter.value === 'assigned') {
                    return row.getAttribute('data-assigned') === '1';
                }
                if (assignmentFilter.value === 'unassigned') {
                    return row.getAttribute('data-assigned') === '0';
                }

                return true;
            };

            var matchesFilters = function (row) {
                var matchesTown = !townFilter.value
                    || row.getAttribute('data-town') === townFilter.value;
                var matchesCycle = !cycleFilter.value
                    || row.getAttribute('data-cycle') === cycleFilter.value;
                var matchesDocumentation = !documentationOnly
                    || row.getAttribute('data-documentation-current') === '1';

                return matchesTown
                    && matchesCycle
                    && matchesState(row)
                    && matchesDocumentation
                    && matchesAssignment(row);
            };

            var applyFilters = function () {
                var visibleCompanies = 0;
                var visibleCollaborations = 0;

                cards.forEach(function (card) {
                    var rows = Array.from(card.querySelectorAll('.colaboracion-cycle-row'));
                    var visibleInCompany = 0;

                    rows.forEach(function (row) {
                        var visible = matchesFilters(row);
                        row.hidden = !visible;
                        if (visible) {
                            visibleInCompany += 1;
                            visibleCollaborations += 1;
                        }
                    });

                    card.hidden = visibleInCompany === 0;
                    var count = card.querySelector('.js-company-cycle-count');
                    if (count) {
                        count.textContent = visibleInCompany.toString();
                    }
                    if (visibleInCompany > 0) {
                        visibleCompanies += 1;
                    }
                });

                companiesOutput.textContent = visibleCompanies.toString();
                collaborationsOutput.textContent = visibleCollaborations.toString();
                emptyState.hidden = visibleCollaborations !== 0;
            };

            [townFilter, cycleFilter, stateFilter, assignmentFilter].forEach(function (filter) {
                filter.addEventListener('change', applyFilters);
            });

            documentationFilter.addEventListener('click', function () {
                documentationOnly = !documentationOnly;
                documentationFilter.classList.toggle('btn-primary', documentationOnly);
                documentationFilter.classList.toggle('btn-default', !documentationOnly);
                documentationFilter.setAttribute('aria-pressed', documentationOnly ? 'true' : 'false');
                applyFilters();
            });

            clearFilters.addEventListener('click', function () {
                townFilter.value = '';
                cycleFilter.value = '';
                stateFilter.value = '';
                assignmentFilter.value = '';
                documentationOnly = false;
                documentationFilter.classList.remove('btn-primary');
                documentationFilter.classList.add('btn-default');
                documentationFilter.setAttribute('aria-pressed', 'false');
                applyFilters();
            });

            applyFilters();
        });
    </script>
@endpush
