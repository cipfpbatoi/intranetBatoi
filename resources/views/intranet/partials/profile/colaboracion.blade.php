@once
    <div class="col-xs-12 mb-3">
        <a href="{{ route('colaboracion.index') }}" class="btn btn-default btn-sm">
            <em class="fa fa-building"></em> Vore colaboraciones del departament
        </a>
    </div>
    @push('styles')
        <style>
            .mis-colaboraciones-town-cards {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 16px;
                width: 100%;
            }

            @media (min-width: 768px) {
                .mis-colaboraciones-town-cards {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (min-width: 1200px) {
                .mis-colaboraciones-town-cards {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            @media (min-width: 1600px) {
                .mis-colaboraciones-town-cards {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }
            }

            @media (min-width: 2000px) {
                .mis-colaboraciones-town-cards {
                    grid-template-columns: repeat(5, minmax(0, 1fr));
                }
            }

            .mis-colaboraciones-town-cards .mis-colaboraciones-card {
                min-width: 0;
                width: 100%;
            }

            .mis-colaboraciones-summary-row {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
            }

            .mis-colaboraciones-summary-card {
                flex: 1 1 180px;
                min-width: 0;
            }

            .mis-colaboraciones-summary-row.mis-colaboraciones-summary-row--four .mis-colaboraciones-summary-card {
                flex-basis: calc(25% - 9px);
                min-width: 0;
            }

            @media (min-width: 992px) {
                .mis-colaboraciones-summary-row.mis-colaboraciones-summary-row--four {
                    display: grid;
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: 12px;
                }
            }

            .mis-colaboraciones-summary-card .small.text-muted {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>
    @endpush
@endonce

@php
    $elementos = $panel->getElementos($pestana)->sortBy('localidad')->values();
    $townOptions = $elementos->pluck('localidad')->filter()->unique()->values();
    $groupedByTown = $elementos->groupBy(static fn ($item) => $item->localidad ?: 'Desconeguda');
    $tabName = $pestana->getNombre();
    $filterId = 'mis-colaboraciones-town-filter-' . $tabName;
    $quickFilterId = 'mis-colaboraciones-quick-filters-' . $tabName;
    $extraQuickFilterId = 'mis-colaboraciones-extra-quick-filters-' . $tabName;
    $summaryData = match ($tabName) {
        'resta' => [
            'type' => 'resta',
            'total' => $elementos->count(),
            'topTowns' => $groupedByTown
                ->map(static fn ($items, $town) => ['town' => $town, 'count' => $items->count()])
                ->sortByDesc('count')
                ->values()
                ->take(2),
        ],
        'pendiente' => [
            'type' => 'pendiente',
            'total' => $elementos->count(),
            'noColaboren' => $elementos->where('estado', 3)->count(),
            'contactades' => $elementos->filter(static fn ($item) => $item->ultimaActividad !== null)->count(),
            'noContactades' => $elementos->filter(static fn ($item) => $item->ultimaActividad === null)->count(),
        ],
        'colabora' => [
            'type' => 'colabora',
            'total' => $elementos->count(),
            'llocsTreball' => $elementos->sum(static fn ($item) => (int) ($item->puestos ?? 0)),
            'fcts' => $elementos->sum(static fn ($item) => (int) ($item->fctsAssociadesCount ?? 0)),
            'reservades' => $elementos->sum(static function ($item) {
                $llocs = (int) ($item->puestos ?? 0);
                $fcts = (int) ($item->fctsAssociadesCount ?? 0);
                $preasignaciones = $item->preasignacionesPanel ?? collect();
                $active = $preasignaciones->whereIn('estado', ['proposta', 'reservada'])->count();
                $llocsDisponibles = max($llocs - $fcts, 0);

                return min($active, $llocsDisponibles);
            }),
            'noAssignades' => $elementos->sum(static function ($item) {
                $llocs = (int) ($item->puestos ?? 0);
                $fcts = (int) ($item->fctsAssociadesCount ?? 0);
                $preasignaciones = $item->preasignacionesPanel ?? collect();
                $reservades = min(
                    $preasignaciones->whereIn('estado', ['proposta', 'reservada'])->count(),
                    max($llocs - $fcts, 0)
                );

                return max($llocs - $fcts - $reservades, 0);
            }),
        ],
        default => [
            'type' => 'generic',
            'total' => $elementos->count(),
        ],
    };
@endphp

@if ($summaryData['type'] === 'resta')
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted" title="Total no assignades">Total no assignades</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['total'] }}</div>
            </div>
        </div>
        @foreach ($summaryData['topTowns'] as $topTown)
            <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
                <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                    <div class="small text-muted" title="{{ $topTown['town'] }}">{{ $topTown['town'] }}</div>
                    <div style="font-size: 1.75rem; font-weight: 700;">{{ $topTown['count'] }}</div>
                </div>
            </div>
        @endforeach
        @if ($summaryData['total'] > $summaryData['topTowns']->sum('count'))
            <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
                <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                    <div class="small text-muted" title="Resta">Resta</div>
                    <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['total'] - $summaryData['topTowns']->sum('count') }}</div>
                </div>
            </div>
        @endif
    </div>
@else
<div class="mb-3 mis-colaboraciones-summary-row">
    @if ($summaryData['type'] === 'pendiente')
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">Total assignades</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['contactades'] + $summaryData['noContactades'] + $summaryData['noColaboren'] }}</div>
            </div>
        </div>
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">Contactades</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['contactades'] }}</div>
            </div>
        </div>
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">No contactades</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['noContactades'] }}</div>
            </div>
        </div>
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">No col·laboren</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['noColaboren'] }}</div>
            </div>
        </div>
    @elseif ($summaryData['type'] === 'colabora')
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">Total</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['total'] }}</div>
            </div>
        </div>
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">Llocs de treball</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['llocsTreball'] }}</div>
            </div>
        </div>
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">FCT</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['fcts'] }}</div>
            </div>
        </div>
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">Reservades</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['reservades'] }}</div>
            </div>
        </div>
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">No assignades</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['noAssignades'] }}</div>
            </div>
        </div>
    @else
        <div class="mis-colaboraciones-summary-card">
            <div class="rounded border p-3 h-100 text-center" style="box-shadow: 0 1px 2px rgba(0,0,0,.04);background:#fff;">
                <div class="small text-muted">{{ $tabName }}</div>
                <div style="font-size: 1.75rem; font-weight: 700;">{{ $summaryData['total'] }}</div>
            </div>
        </div>
    @endif
</div>
@endif

<div class="col-xs-12 mb-3">
    <div style="max-width: 420px;">
        <label for="{{ $filterId }}" class="form-label fw-semibold">Filtrar per poble</label>
        <input id="{{ $filterId }}"
               class="form-control mis-colaboraciones-town-filter"
               data-target-tab="{{ $tabName }}"
               list="{{ $filterId }}-options"
               type="search"
               placeholder="Escriu part del poble">
        <datalist id="{{ $filterId }}-options">
            @foreach ($townOptions as $townOption)
                <option value="{{ $townOption }}"></option>
            @endforeach
        </datalist>
    </div>
</div>

@if ($tabName !== 'resta')
    <div class="col-xs-12 mb-3" id="{{ $quickFilterId }}">
        <p class="fw-semibold mb-2">Filtres ràpids</p>
        @if ($tabName === 'pendiente')
            <div class="d-flex flex-wrap gap-2">
                <label class="btn btn-default btn-sm mb-1">
                    <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-contact">
                    Sense contacte
                </label>
                <label class="btn btn-default btn-sm mb-1">
                    <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-email">
                    Sense email
                </label>
                <label class="btn btn-default btn-sm mb-1">
                    <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-phone">
                    Sense telèfon
                </label>
            </div>
        @else
            <div class="d-flex flex-wrap align-items-start" style="gap: 12px 24px;">
                <div>
                    <p class="small text-muted mb-1"><strong>Fitxa</strong></p>
                    <div class="d-flex flex-wrap gap-2">
                        <label class="btn btn-default btn-sm mb-1">
                            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-contact">
                            Sense contacte
                        </label>
                        <label class="btn btn-default btn-sm mb-1">
                            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-email">
                            Sense email
                        </label>
                        <label class="btn btn-default btn-sm mb-1">
                            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-phone">
                            Sense telèfon
                        </label>
                        <label class="btn btn-default btn-sm mb-1">
                            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-instructor">
                            Sense instructor
                        </label>
                        <label class="btn btn-default btn-sm mb-1">
                            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="not-ready">
                            No preparada
                        </label>
                    </div>
                </div>
                <div>
                    <p class="small text-muted mb-1"><strong>Seguiment</strong></p>
                    <div class="d-flex flex-wrap gap-2">
                        <label class="btn btn-default btn-sm mb-1">
                            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="stale-contact">
                            30+ dies sense contacte
                        </label>
                        <label class="btn btn-default btn-sm mb-1">
                            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="pending-response">
                            Esperant resposta
                        </label>
                        <label class="btn btn-default btn-sm mb-1">
                            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="overdue-followup">
                            Acció vençuda
                        </label>
                    </div>
                </div>
                <div>
                    <p class="small text-muted mb-1"><strong>Documentació</strong></p>
                    <button class="btn btn-default btn-sm mb-1" type="button" data-bs-toggle="collapse"
                            data-bs-target="#{{ $extraQuickFilterId }}" aria-expanded="false"
                            aria-controls="{{ $extraQuickFilterId }}">
                        Més filtres documentals
                    </button>
                </div>
            </div>
        @endif
    </div>

    @if ($tabName === 'colabora')
        <div class="col-xs-12 mb-3 collapse" id="{{ $extraQuickFilterId }}">
            <div class="d-flex flex-wrap gap-2">
                <label class="btn btn-default btn-sm mb-1">
                    <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="pending-agreement">
                    Conveni pendent
                </label>
                <label class="btn btn-default btn-sm mb-1">
                    <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-company-document">
                    Sense document empresa
                </label>
                <label class="btn btn-default btn-sm mb-1">
                    <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="without-fcts">
                    Sense FCT associada
                </label>
                <label class="btn btn-default btn-sm mb-1">
                    <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="documentation-pending">
                    Documentació pendent
                </label>
                <label class="btn btn-default btn-sm mb-1">
                    <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="this-week-followup">
                    Acció esta setmana
                </label>
            </div>
        </div>
    @endif
@endif

@foreach ($groupedByTown as $localidad => $items)
    <div class="col-xs-12 mis-colaboraciones-town-group" data-target-tab="{{ $tabName }}" data-town="{{ $localidad }}" style="margin-top: 8px; margin-bottom: 6px;">
        <div style="padding: 6px 10px; border-left: 4px solid #1abb9c; background: #f7f9fb;">
            <strong><em class="fa fa-map-marker"></em> {{ $localidad }}</strong>
        </div>
    </div>
    <div class="mis-colaboraciones-town-cards" data-target-tab="{{ $tabName }}" data-town="{{ $localidad }}">
        @foreach ($items as $elemento)
            @php($contactos = $elemento->contactos ?? collect())
            @include ('intranet.partials.profile.partials.colaboracion')
        @endforeach
    </div>
@endforeach

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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

                var parseNumber = function (value, fallback) {
                    var parsed = parseInt(value || '', 10);
                    return Number.isNaN(parsed) ? fallback : parsed;
                };

                var compareText = function (left, right) {
                    return left.localeCompare(right, 'ca', { sensitivity: 'base' });
                };

                var quickFilterMatches = function (card, filterName) {
                    var daysWithoutContact = parseNumber(card.getAttribute('data-days-without-contact'), NaN);

                    switch (filterName) {
                        case 'missing-contact':
                            return card.getAttribute('data-has-contact') !== '1';
                        case 'stale-contact':
                            return Number.isNaN(daysWithoutContact) || daysWithoutContact >= 30;
                        case 'missing-email':
                            return card.getAttribute('data-has-email') !== '1';
                        case 'missing-phone':
                            return card.getAttribute('data-has-phone') !== '1';
                        case 'missing-instructor':
                            return card.getAttribute('data-has-instructor') !== '1';
                        case 'pending-agreement':
                            return card.getAttribute('data-conveni-pendent') === '1';
                        case 'missing-company-document':
                            return card.getAttribute('data-has-company-document') !== '1';
                        case 'without-fcts':
                            return card.getAttribute('data-has-fcts') !== '1';
                        case 'not-ready':
                            return card.getAttribute('data-preparation-state') === 'no_preparada';
                        case 'documentation-pending':
                            return parseNumber(card.getAttribute('data-documentation-pending-count'), 0) > 0;
                        case 'pending-response':
                            return card.getAttribute('data-followup-status') === 'pendent_resposta';
                        case 'overdue-followup':
                            return card.getAttribute('data-followup-urgency') === 'vençut';
                        case 'this-week-followup':
                            return card.getAttribute('data-followup-urgency') === 'esta_setmana';
                        default:
                            return true;
                    }
                };

                var cardCompare = function (left, right) {
                    var leftTown = normalizeTown(left.getAttribute('data-town') || '');
                    var rightTown = normalizeTown(right.getAttribute('data-town') || '');
                    var leftCompany = (left.getAttribute('data-company') || '').toString();
                    var rightCompany = (right.getAttribute('data-company') || '').toString();
                    var townCompare = compareText(leftTown, rightTown);

                    if (townCompare !== 0) {
                        return townCompare;
                    }

                    return compareText(leftCompany, rightCompany);
                };

                var collectTownBlocks = function (targetTab) {
                    var groups = Array.from(document.querySelectorAll('.mis-colaboraciones-town-group[data-target-tab="' + targetTab + '"]'));

                    return groups.map(function (group) {
                        var cardsContainer = group.nextElementSibling;
                        var cards = cardsContainer
                            ? Array.from(cardsContainer.querySelectorAll('.mis-colaboraciones-card'))
                            : [];

                        return {
                            group: group,
                            cardsContainer: cardsContainer,
                            cards: cards
                        };
                    });
                };

                var sortTownBlocks = function (blocks) {
                    blocks.forEach(function (block) {
                        block.cards.sort(function (left, right) {
                            return cardCompare(left, right);
                        });
                    });

                    blocks.sort(function (left, right) {
                        return compareText(
                            normalizeTown(left.group.getAttribute('data-town') || ''),
                            normalizeTown(right.group.getAttribute('data-town') || '')
                        );
                    });

                    return blocks;
                };

                var applyOrderingForTab = function (targetTab) {
                    var blocks = collectTownBlocks(targetTab);

                    if (!blocks.length) {
                        return;
                    }

                    var parent = blocks[0].group.parentElement;

                    sortTownBlocks(blocks).forEach(function (block) {
                        parent.appendChild(block.group);
                        if (block.cardsContainer) {
                            parent.appendChild(block.cardsContainer);
                            block.cards.forEach(function (card) {
                                block.cardsContainer.appendChild(card);
                            });
                        }
                    });
                };

                var applyTownGroupVisibilityForTab = function (targetTab) {
                    document.querySelectorAll('.mis-colaboraciones-town-group[data-target-tab="' + targetTab + '"]').forEach(function (group) {
                        var groupTown = normalizeTown(group.getAttribute('data-town') || '');
                        var townFilter = document.querySelector('.mis-colaboraciones-town-filter[data-target-tab="' + targetTab + '"]');
                        var selectedTown = normalizeTown(townFilter ? townFilter.value : '');
                        var matchesTown = !selectedTown || groupTown.indexOf(selectedTown) !== -1;
                        var hasVisibleCards = false;
                        var cardsContainer = group.nextElementSibling;
                        var cards = cardsContainer
                            ? Array.from(cardsContainer.querySelectorAll('.mis-colaboraciones-card'))
                            : [];

                        cards.forEach(function (card) {
                            if (card.style.display !== 'none') {
                                hasVisibleCards = true;
                            }
                        });

                        if (cardsContainer) {
                            cardsContainer.style.display = matchesTown && hasVisibleCards ? 'flex' : 'none';
                        }
                        group.style.display = matchesTown && hasVisibleCards ? '' : 'none';
                    });
                };

                var applyFiltersForTab = function (targetTab) {
                    var townFilter = document.querySelector('.mis-colaboraciones-town-filter[data-target-tab="' + targetTab + '"]');
                    var selectedTown = normalizeTown(townFilter ? townFilter.value : '');
                    var activeQuickFilters = Array.from(
                        document.querySelectorAll('.mis-colaboraciones-quick-filter[data-target-tab="' + targetTab + '"]:checked')
                    ).map(function (input) {
                        return input.getAttribute('data-filter') || '';
                    });

                    document.querySelectorAll('.mis-colaboraciones-card[data-target-tab="' + targetTab + '"]').forEach(function (card) {
                        var cardTown = normalizeTown(card.getAttribute('data-town') || '');
                        var matchesTown = !selectedTown || cardTown.indexOf(selectedTown) !== -1;
                        var matchesQuickFilters = activeQuickFilters.every(function (filterName) {
                            return quickFilterMatches(card, filterName);
                        });

                        card.style.display = matchesTown && matchesQuickFilters ? '' : 'none';
                    });

                    applyOrderingForTab(targetTab);
                    applyTownGroupVisibilityForTab(targetTab);
                };

                document.querySelectorAll('.mis-colaboraciones-town-filter').forEach(function (filter) {
                    var targetTab = filter.getAttribute('data-target-tab') || '';

                    filter.addEventListener('input', function () {
                        applyFiltersForTab(targetTab);
                    });
                    filter.addEventListener('change', function () {
                        applyFiltersForTab(targetTab);
                    });
                    applyFiltersForTab(targetTab);
                });

                document.querySelectorAll('.mis-colaboraciones-quick-filter').forEach(function (filter) {
                    var targetTab = filter.getAttribute('data-target-tab') || '';

                    filter.addEventListener('change', function () {
                        applyFiltersForTab(targetTab);
                    });
                });
            });
        </script>
    @endpush
@endonce
