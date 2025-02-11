<div class="container mt-4">
    <div class="row">
        <!-- 📆 COL 1: CALENDARI (66%) -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                @if ($mes != 9)
                    <button wire:click="canviarMes(-1)" class="btn btn-secondary">← Mes Anterior</button>
                @endif
                <strong class="text-center fw-bold">{{ \Carbon\Carbon::create($any, $mes, 1)->translatedFormat('F Y')  }}</strong>
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
                        <td class="p-3 border cursor-pointer"
                            wire:click="seleccionarDia({{ $dia }})"
                            x-on:click="$dispatch('obrir-modal')"
                            style="background-color:
                                    {{ $dies[$dia]['esdeveniment'] ? '#00aaff' :
                                    ($dies[$dia]['tipus'] == 'festiu' ? '#ffadad' :
                                    ($dies[$dia]['tipus'] == 'no lectiu' ? '#ffd6a5' : '#caffbf')) }};">
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
    </div>

    <!-- MODAL -->
    @if ($seleccionat)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50"
             x-data="{ show: true }"
             x-show="show"
             @tancarModal.window="show = false">
            <div class="bg-white p-6 rounded shadow-lg max-w-md">
                <h3 class="text-xl font-bold mb-4">Modificar {{ $seleccionat }}/{{ $mes }}/{{ $any }}</h3>

                <label for="tipus" class="block mb-2 font-semibold">Tipus de dia:</label>
                <select wire:model="tipus" id="tipus" class="border p-2 w-full rounded">
                    <option value="lectiu">Lectiu</option>
                    <option value="no lectiu">No Lectiu</option>
                    <option value="festiu">Festiu</option>
                </select>

                <label for="esdeveniment" class="block mt-4 mb-2 font-semibold">Esdeveniment:</label>
                <input wire:model="esdeveniment" id="esdeveniment" type="text" class="border p-2 w-full rounded">

                <div class="flex justify-between mt-6">
                    <button wire:click="resetSeleccionat" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel·lar</button>
                    <button wire:click="guardarCanvis" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>