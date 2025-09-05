<div class="row">
    <!-- 📆 COL 1: CALENDARI (66%) -->
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @if ($mes != 9)
                <button wire:click="canviarMes(-1)" class="btn btn-secondary">← Mes Anterior</button>
            @endif

            <strong class="text-center fw-bold">
                {{ \Carbon\Carbon::create($any, $mes, 1)->translatedFormat('F Y') }}
            </strong>

            @if ($mes != 7)
                <button wire:click="canviarMes(1)" class="btn btn-secondary">Mes Següent →</button>
            @endif
        </div>

        <table class="table table-bordered text-center">
            <thead class="table-light">
            <tr>
                @foreach(['Dl', 'Dt', 'Dc', 'Dj', 'Dv', 'Ds', 'Dg'] as $dia)
                    <th>{{ $dia }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @php
                $primerDiaSetmana = \Carbon\Carbon::create($any, $mes, 1)->dayOfWeekIso;
                $diesMes = \Carbon\Carbon::create($any, $mes, 1)->daysInMonth;
                $colspan = $primerDiaSetmana - 1;
            @endphp

            <tr>
                @if ($colspan > 0)
                    <td colspan="{{ $colspan }}"></td>
                @endif

                @foreach(range(1, $diesMes) as $dia)
                    @php
                        $info = $dies[$dia] ?? ['tipus' => 'lectiu', 'esdeveniment' => ''];
                        $bg = $info['esdeveniment'] ? '#00aaff'
                            : ($info['tipus'] == 'festiu' ? '#ffadad'
                            : ($info['tipus'] == 'no lectiu' ? '#ffd6a5' : '#caffbf'));
                    @endphp

                    <td class="p-3 border" wire:key="dia-{{ $any }}-{{ $mes }}-{{ $dia }}"
                        wire:click="seleccionarDia({{ $dia }})"
                        style="cursor:pointer;background-color: {{ $bg }};">
                        <strong>{{ $dia }}</strong>
                    </td>

                    @if (($colspan + $dia) % 7 == 0)
            </tr><tr>
                @endif
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>

    <!-- 📋 COL 2: LLISTA D’ESDEVENIMENTS (33%) -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-center fw-bold">📅 Esdeveniments del mes</div>
            <div class="card-body">
                @if (count($esdeveniments) > 0)
                    <ul class="list-group">
                        @foreach($esdeveniments as $ev)
                            <li class="list-group-item">
                                <strong>{{ $ev['dia'] }}</strong> - {{ $ev['esdeveniment'] }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-center text-muted">No hi ha esdeveniments aquest mes.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL (només existeix si hi ha dia seleccionat) -->
    @if ($seleccionat)
        <div class="fixed inset-0 d-flex align-items-center justify-content-center"
             style="background:rgba(0,0,0,.5);position:fixed;top:0;left:0;right:0;bottom:0;z-index:1050;">
            <div class="bg-white p-4 rounded shadow-lg" style="width:100%;max-width:520px;">
                <h3 class="h5 fw-bold mb-3">Modificar {{ $seleccionat }}/{{ $mes }}/{{ $any }}</h3>

                <label for="tipus" class="form-label fw-semibold">Tipus de dia:</label>
                <select wire:model="tipus" id="tipus" class="form-select">
                    <option value="lectiu">Lectiu</option>
                    <option value="no lectiu">No Lectiu</option>
                    <option value="festiu">Festiu</option>
                </select>

                <label for="esdeveniment" class="form-label fw-semibold mt-3">Esdeveniment:</label>
                <input wire:model="esdeveniment" id="esdeveniment" type="text" class="form-control">

                <div class="d-flex justify-content-between mt-4">
                    <button wire:click="resetSeleccionat" class="btn btn-secondary">Cancel·lar</button>
                    <button wire:click="guardarCanvis" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
