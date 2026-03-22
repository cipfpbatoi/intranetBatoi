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
@endphp

<div class="col-xs-12 mb-3">
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

@foreach ($elementos as $elemento)
    @php
        $contactos = $elemento->contactos ?? collect();
        $isMine = $elemento->tutor === authUser()->dni;
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

                document.querySelectorAll('.mis-colaboraciones-town-filter').forEach(function (filter) {
                    var applyTownFilter = function () {
                        var selectedTown = normalizeTown(filter.value);
                        var targetTab = filter.getAttribute('data-target-tab') || '';

                        document.querySelectorAll('.mis-colaboraciones-town-group[data-target-tab="' + targetTab + '"]').forEach(function (group) {
                            var groupTown = normalizeTown(group.getAttribute('data-town') || '');
                            var visible = !selectedTown || groupTown.indexOf(selectedTown) !== -1;
                            group.style.display = visible ? '' : 'none';

                            var next = group.nextElementSibling;
                            while (next && !next.classList.contains('mis-colaboraciones-town-group')) {
                                next.style.display = visible ? '' : 'none';
                                next = next.nextElementSibling;
                            }
                        });
                    };

                    filter.addEventListener('input', applyTownFilter);
                    filter.addEventListener('change', applyTownFilter);
                });
            });
        </script>
    @endpush
@endonce
