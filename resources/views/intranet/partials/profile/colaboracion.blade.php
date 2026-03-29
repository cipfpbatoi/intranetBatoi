@once
    <div class="col-xs-12 mb-3">
        <a href="{{ route('colaboracion.index') }}" class="btn btn-default btn-sm">
            <em class="fa fa-building"></em> Vore colaboraciones del departament
        </a>
    </div>
@endonce

@php
    $elementos = $panel->getElementos($pestana)->sortBy('localidad')->values();
    $localidadActual = null;
    $townOptions = $elementos->pluck('localidad')->filter()->unique()->values();
    $tabName = $pestana->getNombre();
    $filterId = 'mis-colaboraciones-town-filter-' . $tabName;
    $quickFilterId = 'mis-colaboraciones-quick-filters-' . $tabName;
    $sortId = 'mis-colaboraciones-sort-' . $tabName;
@endphp

<div class="col-xs-12 mb-3">
    <div class="d-flex flex-wrap align-items-end" style="gap: 12px;">
        <div style="flex: 2 1 320px; min-width: 260px;">
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

        <div style="flex: 1 1 220px; min-width: 220px;">
            <label for="{{ $sortId }}" class="form-label fw-semibold">Ordenar per</label>
            <select id="{{ $sortId }}" class="form-control mis-colaboraciones-sort" data-target-tab="{{ $tabName }}">
                <option value="locality">Poble</option>
                <option value="priority">Prioritat</option>
                <option value="stale">Desactualització</option>
                <option value="company">Empresa</option>
            </select>
        </div>
    </div>
</div>

<div class="col-xs-12 mb-3" id="{{ $quickFilterId }}">
    <p class="fw-semibold mb-2">Filtres ràpids</p>
    <div class="d-flex flex-wrap gap-2">
        <label class="btn btn-default btn-sm mb-1">
            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="missing-contact">
            Sense contacte
        </label>
        <label class="btn btn-default btn-sm mb-1">
            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="stale-contact">
            30+ dies sense contacte
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
            <input type="checkbox" class="mis-colaboraciones-quick-filter" data-target-tab="{{ $tabName }}" data-filter="pending-agreement">
            Conveni pendent
        </label>
    </div>
</div>

@foreach ($elementos as $elemento)
    @php
        $contactos = $elemento->contactos ?? collect();
        $localidad = $elemento->localidad;
    @endphp
    @if ($localidadActual !== $localidad)
        @php($localidadActual = $localidad)
        <div class="col-xs-12 mis-colaboraciones-town-group" data-target-tab="{{ $tabName }}" data-town="{{ $localidadActual }}" style="margin-top: 8px; margin-bottom: 6px;">
            <div style="padding: 6px 10px; border-left: 4px solid #1abb9c; background: #f7f9fb;">
                <strong><em class="fa fa-map-marker"></em> {{ $localidadActual }}</strong>
            </div>
        </div>
    @endif
    @include ('intranet.partials.profile.partials.colaboracion')
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
                        default:
                            return true;
                    }
                };

                var cardCompare = function (left, right, orderMode) {
                    var leftTown = normalizeTown(left.getAttribute('data-town') || '');
                    var rightTown = normalizeTown(right.getAttribute('data-town') || '');
                    var leftCompany = (left.getAttribute('data-company') || '').toString();
                    var rightCompany = (right.getAttribute('data-company') || '').toString();
                    var leftDays = parseNumber(left.getAttribute('data-days-without-contact'), 9999);
                    var rightDays = parseNumber(right.getAttribute('data-days-without-contact'), 9999);
                    var leftPriority = parseNumber(left.getAttribute('data-priority-score'), 0);
                    var rightPriority = parseNumber(right.getAttribute('data-priority-score'), 0);

                    if (orderMode === 'priority') {
                        if (rightPriority !== leftPriority) {
                            return rightPriority - leftPriority;
                        }
                        return compareText(leftCompany, rightCompany);
                    }

                    if (orderMode === 'stale') {
                        if (rightDays !== leftDays) {
                            return rightDays - leftDays;
                        }
                        return compareText(leftCompany, rightCompany);
                    }

                    if (orderMode === 'company') {
                        var companyCompare = compareText(leftCompany, rightCompany);
                        if (companyCompare !== 0) {
                            return companyCompare;
                        }
                        return compareText(leftTown, rightTown);
                    }

                    var townCompare = compareText(leftTown, rightTown);
                    if (townCompare !== 0) {
                        return townCompare;
                    }

                    return compareText(leftCompany, rightCompany);
                };

                var collectTownBlocks = function (targetTab) {
                    var groups = Array.from(document.querySelectorAll('.mis-colaboraciones-town-group[data-target-tab="' + targetTab + '"]'));

                    return groups.map(function (group) {
                        var cards = [];
                        var next = group.nextElementSibling;

                        while (next && !next.classList.contains('mis-colaboraciones-town-group')) {
                            if (next.classList.contains('mis-colaboraciones-card')) {
                                cards.push(next);
                            }
                            next = next.nextElementSibling;
                        }

                        return {
                            group: group,
                            cards: cards
                        };
                    });
                };

                var sortTownBlocks = function (blocks, orderMode) {
                    blocks.forEach(function (block) {
                        block.cards.sort(function (left, right) {
                            return cardCompare(left, right, orderMode);
                        });
                    });

                    if (orderMode === 'locality') {
                        blocks.sort(function (left, right) {
                            return compareText(
                                normalizeTown(left.group.getAttribute('data-town') || ''),
                                normalizeTown(right.group.getAttribute('data-town') || '')
                            );
                        });
                        return blocks;
                    }

                    blocks.sort(function (left, right) {
                        var leftVisibleCards = left.cards.filter(function (card) {
                            return card.style.display !== 'none';
                        });
                        var rightVisibleCards = right.cards.filter(function (card) {
                            return card.style.display !== 'none';
                        });
                        var leftAnchor = leftVisibleCards[0] || left.cards[0];
                        var rightAnchor = rightVisibleCards[0] || right.cards[0];

                        return cardCompare(leftAnchor, rightAnchor, orderMode);
                    });

                    return blocks;
                };

                var applyOrderingForTab = function (targetTab) {
                    var sortSelect = document.querySelector('.mis-colaboraciones-sort[data-target-tab="' + targetTab + '"]');
                    var orderMode = sortSelect ? sortSelect.value : 'locality';
                    var blocks = collectTownBlocks(targetTab);

                    if (!blocks.length) {
                        return;
                    }

                    var parent = blocks[0].group.parentElement;

                    if (orderMode === 'locality') {
                        sortTownBlocks(blocks, orderMode).forEach(function (block) {
                            parent.appendChild(block.group);
                            block.cards.forEach(function (card) {
                                parent.appendChild(card);
                            });
                        });

                        return;
                    }

                    document.querySelectorAll('.mis-colaboraciones-town-group[data-target-tab="' + targetTab + '"]').forEach(function (group) {
                        group.style.display = 'none';
                    });

                    blocks
                        .reduce(function (allCards, block) {
                            return allCards.concat(block.cards);
                        }, [])
                        .sort(function (left, right) {
                            return cardCompare(left, right, orderMode);
                        })
                        .forEach(function (card) {
                            parent.appendChild(card);
                        });
                };

                var applyTownGroupVisibilityForTab = function (targetTab) {
                    var sortSelect = document.querySelector('.mis-colaboraciones-sort[data-target-tab="' + targetTab + '"]');
                    var orderMode = sortSelect ? sortSelect.value : 'locality';

                    if (orderMode !== 'locality') {
                        document.querySelectorAll('.mis-colaboraciones-town-group[data-target-tab="' + targetTab + '"]').forEach(function (group) {
                            group.style.display = 'none';
                        });

                        return;
                    }

                    document.querySelectorAll('.mis-colaboraciones-town-group[data-target-tab="' + targetTab + '"]').forEach(function (group) {
                        var groupTown = normalizeTown(group.getAttribute('data-town') || '');
                        var townFilter = document.querySelector('.mis-colaboraciones-town-filter[data-target-tab="' + targetTab + '"]');
                        var selectedTown = normalizeTown(townFilter ? townFilter.value : '');
                        var matchesTown = !selectedTown || groupTown.indexOf(selectedTown) !== -1;
                        var hasVisibleCards = false;
                        var next = group.nextElementSibling;

                        while (next && !next.classList.contains('mis-colaboraciones-town-group')) {
                            if (next.classList.contains('mis-colaboraciones-card') && next.style.display !== 'none') {
                                hasVisibleCards = true;
                            }
                            next = next.nextElementSibling;
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

                document.querySelectorAll('.mis-colaboraciones-sort').forEach(function (sortSelect) {
                    var targetTab = sortSelect.getAttribute('data-target-tab') || '';

                    sortSelect.addEventListener('change', function () {
                        applyOrderingForTab(targetTab);
                    });
                });
            });
        </script>
    @endpush
@endonce
