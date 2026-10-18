@once
    @push('styles')
        <style>
            .colaboracion-view-toolbar {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px;
            }

            .colaboracion-view-toolbar .btn {
                margin: 0;
            }

            .colaboracion-group + .colaboracion-group {
                margin-top: 22px;
            }

            .colaboracion-group-title {
                display: flex;
                align-items: center;
                gap: 8px;
                margin: 0 0 10px;
                padding-bottom: 6px;
                border-bottom: 1px solid #dfe5eb;
                color: #34495e;
                font-size: 1rem;
                font-weight: 700;
            }

            .colaboracion-card-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 12px;
            }

            .colaboracion-department-card {
                display: block;
                min-width: 0;
                height: 100%;
                padding: 12px 14px;
                border: 1px solid #d7dee6;
                border-left: 4px solid #7f8c8d;
                border-radius: 5px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
            }

            .colaboracion-department-card.is-own-cycle {
                border-left-color: #337ab7;
                background: #f8fbfe;
            }

            .colaboracion-department-card:hover,
            .colaboracion-department-card:focus {
                border-color: #78aee8;
                background: #f5f9fd;
            }

            .colaboracion-card-company {
                overflow: hidden;
                margin: 0;
                color: #144a75;
                font-size: 1rem;
                font-weight: 700;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .colaboracion-card-meta,
            .colaboracion-card-badges {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px;
            }

            @media (min-width: 768px) {
                .colaboracion-card-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (min-width: 1200px) {
                .colaboracion-card-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }
        </style>
    @endpush
@endonce

@php
    $elementos = $panel->getElementos($pestana)
        ->sortBy([
            ['localidad', 'asc'],
            ['empresa', 'asc'],
            ['idCiclo', 'asc'],
        ]);
    $townGroups = $elementos->groupBy(static fn ($elemento) => $elemento->localidad ?: 'Desconeguda');
    $viewCounts = [
        'town' => $elementos->count(),
        'cycle' => $elementos->count(),
        'contacted' => $elementos->filter(static fn ($elemento) => (int) ($elemento->estado ?? 0) !== 0)->count(),
        'collaborates' => $elementos->where('estado', 2)->count(),
        'documentation' => $elementos->filter(static fn ($elemento) => !empty($elemento->conveniPendent))->count(),
        'inactive' => $elementos->filter(static fn ($elemento) => !empty($elemento->senseActivitatRecent))->count(),
    ];
    $status = static function ($elemento): array {
        return match ((int) ($elemento->estado ?? 0)) {
            2 => ['label' => 'Col·labora', 'class' => 'bg-success'],
            3 => ['label' => 'No col·labora', 'class' => 'bg-danger'],
            1 => ['label' => 'Pendent', 'class' => 'bg-warning text-dark'],
            default => ['label' => null, 'class' => 'bg-secondary'],
        };
    };
@endphp

<div class="x_content" id="colaboracion-department-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="colaboracion-view-toolbar" role="group" aria-label="Classificació de col·laboracions">
            <button type="button" class="btn btn-primary btn-sm js-colaboracion-view" data-view="town" aria-pressed="true">
                <em class="fa fa-map-marker" aria-hidden="true"></em>
                Per poble <span class="badge bg-light text-dark">{{ $viewCounts['town'] }}</span>
            </button>
            <button type="button" class="btn btn-default btn-sm js-colaboracion-view" data-view="cycle" aria-pressed="false">
                <em class="fa fa-graduation-cap" aria-hidden="true"></em>
                Per cicle <span class="badge bg-light text-dark">{{ $viewCounts['cycle'] }}</span>
            </button>
            <button type="button" class="btn btn-default btn-sm js-colaboracion-view" data-view="contacted" aria-pressed="false">
                <em class="fa fa-phone" aria-hidden="true"></em>
                Contactades <span class="badge bg-light text-dark">{{ $viewCounts['contacted'] }}</span>
            </button>
            <button type="button" class="btn btn-default btn-sm js-colaboracion-view" data-view="collaborates" aria-pressed="false">
                <em class="fa fa-check" aria-hidden="true"></em>
                Col·laboren <span class="badge bg-light text-dark">{{ $viewCounts['collaborates'] }}</span>
            </button>
            <button type="button" class="btn btn-default btn-sm js-colaboracion-view" data-view="documentation" aria-pressed="false">
                <em class="fa fa-file-text-o" aria-hidden="true"></em>
                Documentació antiga o pendent
                <span class="badge bg-light text-dark">{{ $viewCounts['documentation'] }}</span>
            </button>
            <button type="button" class="btn btn-default btn-sm js-colaboracion-view" data-view="inactive" aria-pressed="false">
                <em class="fa fa-clock-o" aria-hidden="true"></em>
                Sense activitat recent <span class="badge bg-light text-dark">{{ $viewCounts['inactive'] }}</span>
            </button>
        </div>

        <a href="{{ route('colaboracion.mias') }}" class="btn btn-default btn-sm">
            <em class="fa fa-user" aria-hidden="true"></em> Les meues col·laboracions
        </a>
    </div>

    <p class="small text-muted mb-3">
        Es mostren totes les col·laboracions del departament. La marca «El meu cicle» identifica les del tutor actual.
    </p>

    <div id="colaboracion-department-groups">
        @forelse ($townGroups as $localidad => $townItems)
            <section class="colaboracion-group">
                <h4 class="colaboracion-group-title">
                    <em class="fa fa-map-marker" aria-hidden="true"></em>
                    {{ $localidad }}
                    <span class="badge bg-secondary">{{ $townItems->count() }}</span>
                </h4>

                <div class="colaboracion-card-grid">
                    @foreach ($townItems as $elemento)
                        @php
                            $cycle = optional($elemento->Ciclo);
                            $cycleLabel = $cycle->ciclo
                                ?? $cycle->acronim
                                ?? $cycle->literal
                                ?? ('Cicle ' . ($elemento->idCiclo ?? ''));
                            $cycleTitle = $cycle->literal ?? $cycleLabel;
                            $responsable = trim((string) (optional($elemento->Propietario)->shortName ?? ''));
                            $assigned = !empty($elemento->tutor) && $responsable !== '';
                            $cardStatus = $status($elemento);
                            $lastFctDate = $elemento->ultimaFctData ?? null;
                        @endphp

                        <a href="{{ route('colaboracion.show', ['colaboracion' => $elemento->id]) }}"
                           class="js-colaboracion-show colaboracion-department-card text-decoration-none text-reset{{ !empty($elemento->esCiclePropi) ? ' is-own-cycle' : '' }}"
                           data-show-title="Detall col·laboració"
                           data-company="{{ $elemento->empresa }}"
                           data-town="{{ $localidad }}"
                           data-cycle="{{ $cycleLabel }}"
                           data-contacted="{{ (int) ($elemento->estado ?? 0) !== 0 ? '1' : '0' }}"
                           data-collaborates="{{ (int) ($elemento->estado ?? 0) === 2 ? '1' : '0' }}"
                           data-documentation-old="{{ !empty($elemento->conveniPendent) ? '1' : '0' }}"
                           data-inactive="{{ !empty($elemento->senseActivitatRecent) ? '1' : '0' }}">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h5 class="colaboracion-card-company" title="{{ $elemento->empresa }}">
                                    <em class="fa fa-building" aria-hidden="true"></em>
                                    {{ $elemento->empresa }}
                                </h5>
                                @if (!empty($elemento->esCiclePropi))
                                    <span class="badge bg-primary text-nowrap">El meu cicle</span>
                                @endif
                            </div>

                            <div class="colaboracion-card-meta small text-muted mb-2">
                                <span>
                                    <em class="fa fa-map-marker" aria-hidden="true"></em>
                                    {{ $localidad }}
                                </span>
                                <span class="badge bg-info text-dark" title="{{ $cycleTitle }}">
                                    {{ $cycleLabel }}
                                </span>
                            </div>

                            <div class="small text-muted mb-2">
                                @if ($assigned)
                                    <em class="fa fa-user" aria-hidden="true"></em>
                                    {{ $responsable }}
                                @else
                                    <span class="text-danger" role="img" aria-label="Sense assignar" title="Sense assignar">
                                        <em class="fa fa-user-times" aria-hidden="true"></em>
                                    </span>
                                @endif
                            </div>

                            <div class="colaboracion-card-badges">
                                @if ($cardStatus['label'])
                                    <span class="badge {{ $cardStatus['class'] }}">{{ $cardStatus['label'] }}</span>
                                @endif
                                @if (!empty($elemento->conveniPendent))
                                    <span class="badge bg-danger">Documentació antiga o pendent</span>
                                @endif
                                @if (!empty($elemento->senseActivitatRecent))
                                    <span class="badge bg-secondary">
                                        @if ($lastFctDate)
                                            Última FCT: {{ $lastFctDate->format('d/m/Y') }}
                                        @else
                                            Sense FCT registrada
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="alert alert-info mb-0">
                No hi ha col·laboracions disponibles per al departament.
            </div>
        @endforelse
    </div>

    <div id="colaboracion-department-empty" class="alert alert-info mb-0" hidden>
        No hi ha col·laboracions per a esta classificació.
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var panel = document.getElementById('colaboracion-department-panel');
            var container = document.getElementById('colaboracion-department-groups');
            var emptyState = document.getElementById('colaboracion-department-empty');

            if (!panel || !container || !emptyState) {
                return;
            }

            var buttons = Array.from(panel.querySelectorAll('.js-colaboracion-view'));
            var cards = Array.from(container.querySelectorAll('.colaboracion-department-card'));
            var viewLabels = {
                contacted: 'Contactades',
                collaborates: 'Col·laboren',
                documentation: 'Documentació antiga o pendent',
                inactive: 'Sense activitat recent'
            };

            var matchesView = function (card, view) {
                if (view === 'contacted') {
                    return card.getAttribute('data-contacted') === '1';
                }
                if (view === 'collaborates') {
                    return card.getAttribute('data-collaborates') === '1';
                }
                if (view === 'documentation') {
                    return card.getAttribute('data-documentation-old') === '1';
                }
                if (view === 'inactive') {
                    return card.getAttribute('data-inactive') === '1';
                }

                return true;
            };

            var groupLabel = function (card, view) {
                if (view === 'town') {
                    return card.getAttribute('data-town') || 'Desconeguda';
                }
                if (view === 'cycle') {
                    return card.getAttribute('data-cycle') || 'Sense cicle';
                }

                return viewLabels[view] || 'Col·laboracions';
            };

            var renderView = function (view) {
                var visibleCards = cards.filter(function (card) {
                    return matchesView(card, view);
                });
                var groups = new Map();

                visibleCards.forEach(function (card) {
                    var label = groupLabel(card, view);
                    if (!groups.has(label)) {
                        groups.set(label, []);
                    }
                    groups.get(label).push(card);
                });

                container.innerHTML = '';
                Array.from(groups.keys())
                    .sort(function (left, right) {
                        return left.localeCompare(right, 'ca', {sensitivity: 'base'});
                    })
                    .forEach(function (label) {
                        var section = document.createElement('section');
                        section.className = 'colaboracion-group';

                        var heading = document.createElement('h4');
                        heading.className = 'colaboracion-group-title';
                        heading.textContent = label + ' ';

                        var count = document.createElement('span');
                        count.className = 'badge bg-secondary';
                        count.textContent = groups.get(label).length.toString();
                        heading.appendChild(count);

                        var grid = document.createElement('div');
                        grid.className = 'colaboracion-card-grid';
                        groups.get(label)
                            .sort(function (left, right) {
                                var companyComparison = (left.getAttribute('data-company') || '')
                                    .localeCompare(right.getAttribute('data-company') || '', 'ca', {sensitivity: 'base'});
                                if (companyComparison !== 0) {
                                    return companyComparison;
                                }

                                return (left.getAttribute('data-cycle') || '')
                                    .localeCompare(right.getAttribute('data-cycle') || '', 'ca', {sensitivity: 'base'});
                            })
                            .forEach(function (card) {
                                grid.appendChild(card);
                            });

                        section.appendChild(heading);
                        section.appendChild(grid);
                        container.appendChild(section);
                    });

                buttons.forEach(function (button) {
                    var active = button.getAttribute('data-view') === view;
                    button.classList.toggle('btn-primary', active);
                    button.classList.toggle('btn-default', !active);
                    button.setAttribute('aria-pressed', active ? 'true' : 'false');
                });

                emptyState.hidden = visibleCards.length !== 0;
            };

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    renderView(button.getAttribute('data-view') || 'town');
                });
            });

            renderView('town');
        });
    </script>
@endpush
