 <div class="container mt-4">
    <h2 class="text-center fw-bold mb-4"> {{ curso() }}</h2>
    <div class="row g-3">
        @php
            $anyActual = now()->year;
            $anyAnterior = $anyActual - 1;
            $mesos = [
                9 => 'Setembre', 10 => 'Octubre', 11 => 'Novembre', 12 => 'Desembre',
                1 => 'Gener', 2 => 'Febrer', 3 => 'Març', 4 => 'Abril',
                5 => 'Maig', 6 => 'Juny', 7 => 'Juliol'
            ];
        @endphp

        @foreach($mesos as $mes => $nomMes)
            @php
                $any = ($mes >= 9) ? $anyActual : $anyAnterior;
                $primerDiaSetmana = \Carbon\Carbon::create($any, $mes, 1)->dayOfWeekIso;
                $diesMes = \Carbon\Carbon::create($any, $mes, 1)->daysInMonth;
                $colspan = $primerDiaSetmana - 1;
                $dies = \Intranet\Entities\CalendariEscolar::whereYear('data', $any)
                            ->whereMonth('data', $mes)
                            ->get()
                            ->keyBy('data');

                $totalDies = $colspan + $diesMes; // Total de cel·les reals
                $totalFiles = ceil($totalDies / 7); // Files necessàries
            @endphp

            <div class="col-md-4 d-flex align-items-stretch">
                <div class="card shadow-sm w-100">
                    <div class="card-header text-center fw-bold bg-primary text-white p-1">
                        {{ $nomMes }} {{ $any }}
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-bordered text-center table-sm compact-table">
                            <thead class="table-light">
                            <tr>
                                @foreach(['Dl', 'Dt', 'Dc', 'Dj', 'Dv', 'Ds', 'Dg'] as $dia)
                                    <th class="p-1 text-xs">{{ $dia }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @php $diaCounter = 1; @endphp

                            @for ($fila = 1; $fila <= 6; $fila++) <!-- Fixem sempre 6 files -->
                            <tr class="fixed-height-row">
                                @for ($col = 1; $col <= 7; $col++)
                                    @if ($fila == 1 && $col < $primerDiaSetmana)
                                        <td class="p-1 border text-xs"></td>
                                    @elseif ($diaCounter <= $diesMes)
                                        @php
                                            $data = "$any-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-' . str_pad($diaCounter, 2, '0', STR_PAD_LEFT);
                                            $registre = $dies[$data] ?? null;
                                            $tipus = $registre->tipus ?? 'lectiu';
                                            $esdeveniment = $registre->esdeveniment ?? null;
                                            $color = match ($tipus) {
                                                'festiu' => '#ffadad',
                                                'no lectiu' => '#ffd6a5',
                                                default => '#caffbf'
                                            };
                                        @endphp

                                        <td class="p-1 border text-xs"
                                            style="background-color: {{ $esdeveniment ? '#00aaff' : $color }};"
                                            title="{{ $esdeveniment }}">
                                            <strong>{{ $diaCounter }}</strong>
                                        </td>
                                        @php $diaCounter++; @endphp
                                    @else
                                        <td class="p-1 border text-xs"></td> <!-- Espais buits per mantenir 6 files -->
                                    @endif
                                @endfor
                            </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    /* Ajustem la mida de text i cel·les perquè tot siga compacte */
    .compact-table th, .compact-table td {
        padding: 4px !important;
        font-size: 10px !important;
        min-width: 25px;
        max-width: 30px;
    }

    .compact-table th {
        background-color: #f8f9fa;
    }

    /* Fixem l'alçada de les files per evitar desquadraments */
    .fixed-height-row {
        height: 30px !important;
    }
</style>
