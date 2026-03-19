@php
    $townGroups = $panel->getElementos($pestana)
        ->sortBy([
            ['localidad', 'asc'],
            ['empresa', 'asc'],
        ])
        ->groupBy(fn ($elemento) => $elemento->localidad ?: 'Desconeguda');
    $townOptions = $townGroups->keys()->values();

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

    <div class="mb-3">
        <label for="colaboracion-town-filter" class="form-label fw-semibold">Filtrar per poble</label>
        <select id="colaboracion-town-filter" class="form-control">
            <option value="">Tots els pobles</option>
            @foreach ($townOptions as $townOption)
                <option value="{{ $townOption }}">{{ $townOption }}</option>
            @endforeach
        </select>
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
                <small class="text-muted">{{ $centers->count() }} centre(s)</small>
            </div>

            <div class="row">
                @foreach ($centers as $centerName => $centerItems)
                    <div class="col-md-4 col-sm-6 col-xs-12 mb-3">
                        <div class="rounded border bg-white h-100 p-3"
                             style="box-shadow: 0 1px 2px rgba(0,0,0,.04);">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="badge"
                                          style="background:#e8f3ff;border:1px solid #78aee8;color:#144a75;font-weight:700;letter-spacing:.03em;">
                                        {{ strtoupper((string) $centerName) }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $centerItems->count() }} col·laboració(ns)
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                @foreach ($centerItems as $elemento)
                                    @php
                                        $status = $contactStatus($elemento);
                                        $responsable = $elemento->profesor ?: 'Sense assignar';
                                    @endphp

                                    <a href="{{ route('colaboracion.show', ['colaboracion' => $elemento->id]) }}"
                                       class="text-decoration-none text-reset">
                                        <div class="rounded px-2 py-2"
                                             style="border:1px solid #e6e9ed;background:#fbfbfc;">
                                            <div class="d-flex flex-wrap gap-1 mb-1">
                                                <span class="badge"
                                                      style="{{ $cycleStyle($elemento) }}">
                                                    {{ optional($elemento->Ciclo)->literal ?? ('Cicle ' . ($elemento->idCiclo ?? '')) }}
                                                </span>
                                            </div>

                                            @if ($status['label'])
                                                <div class="small {{ $status['class'] }}">
                                                    {{ $status['label'] }}
                                                    @if (!empty($elemento->tutor))
                                                        · {{ $responsable }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
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

            filter.addEventListener('change', function () {
                var selectedTown = filter.value;

                groups.forEach(function (group) {
                    var groupTown = group.getAttribute('data-town') || '';
                    group.style.display = (!selectedTown || groupTown === selectedTown) ? '' : 'none';
                });
            });
        });
    </script>
@endpush
