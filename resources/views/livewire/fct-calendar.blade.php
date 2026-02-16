<div class="fct-calendar" wire:key="fct-calendar-root">
@if($showConfigForm)
    <div>
        <!-- Autoritzacions -->
        <div class="mt-4 flex items-center">
            <input type="checkbox" id="autorizacion" class="mr-2" wire:model="allowNoLectiu">
            <label for="autorizacion" class="text-sm font-bold">Pot fer pràctiques en dies no lectius</label>
        </div>
        @if($allowNoLectiu)
            <div class="mt-4 flex items-center">
                <input type="checkbox" id="allowFestiu" class="mr-2" wire:model="allowFestiu">
                <label for="allowFestiu" class="text-sm font-bold">Pot fer pràctiques en dies festius</label>
            </div>
        @endif
        <div class="mt-4 flex items-center gap-3">
            <label for="maxHours" class="text-sm font-bold">Límit d’hores FCT</label>
            <input id="maxHours" type="number" min="0" step="0.5"
                   class="border px-2 py-1 w-24"
                   wire:model.number="maxHours">
         </div>

        <!-- Trams -->
        <div class="mt-6">
            <label class="block font-bold mb-2">Defineix els trams i l'horari per setmana:</label>
            <!-- Trams -->
            @foreach($trams as $index => $tram)
                <div class="border p-3 mb-4 rounded bg-gray-50">
                    <div class="flex items-center space-x-2 mb-2">
                        <input type="date" wire:model="trams.{{ $index }}.inici" class="border px-2 py-1">
                        <span>-</span>
                        <input type="date" wire:model="trams.{{ $index }}.fi" class="border px-2 py-1">
                        <button type="button" wire:click="removeTram({{ $index }})" class="bg-red-500 text-white px-2 py-1 rounded">🗑</button>
                    </div>

                    <!-- Select col·laboració -->
                    <div class="mb-2">
                        <label for="colab_{{ $index }}" class="text-sm">Col·laboració (opcional)</label>
                        <select id="colab_{{ $index }}" wire:model="trams.{{ $index }}.colaboracion_id" class="w-full border px-2 py-1 text-sm">
                            <option value="">-- Sense col·laboració --</option>
                            @foreach($colaboraciones as $colab)
                                <option value="{{ $colab->id }}">{{ $colab->Centro->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Taula dies de la setmana -->
                    <table class="table-auto border-collapse border text-xs w-full mt-2">
                        <thead>
                        <tr class="bg-gray-200">
                            @foreach(['Dl','Dt','Dc','Dj','Dv','Ds','Dg'] as $i => $dia)
                                <th class="border px-2 py-1 text-center">{{ $dia }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @for ($d = 1; $d <= 7; $d++)
                                <td class="border px-2 py-1 text-center">
                                    <input type="number"
                                           wire:model="trams.{{ $index }}.hores_setmana.{{ $d }}"
                                           class="border w-12 text-center text-xs"
                                           min="0" max="24" step="0.5">
                                </td>
                            @endfor
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach


            <button type="button" wire:click="addTram" class="mt-2 bg-gray-300 px-3 py-1 rounded text-sm">➕ Afegir tram</button>
        </div>

        <!-- Botó generar calendari -->
        <div class="mt-6">
            <button type="button" wire:click="createCalendar"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                ✅ Generar Calendari
            </button>
        </div>
    </div>
@else
    <div>
        <h2 class="text-lg font-bold mb-4">Calendari de FCT</h2>

        @if (session()->has('email_status'))
            <div class="mb-3 p-3 rounded bg-green-200 text-green-900 text-sm font-bold border-l-4 border-green-600 shadow-sm">
                ✅ {{ session('email_status') }}
            </div>
        @endif

        <div class="flex gap-4 mb-4">
            <button type="button" wire:click="deleteCalendar"
                    class="bg-red-500 text-white px-4 py-2 rounded">
                🗑️ Modificar Calendari
            </button>

            <button type="button" wire:click="exportCalendarPdf"
                    class="bg-green-500 text-white px-4 py-2 rounded">
                📄 Descarregar PDF
            </button>
            <button type="button" wire:click="sendCalendarEmails"
                    class="bg-indigo-500 text-white px-4 py-2 rounded">
                ✉️ Enviar per correu
            </button>
        </div>

        <table class="table-auto border-collapse border w-full text-xs">
            <thead>
            <tr class="bg-gray-200">
                <th class="border px-2 py-1 text-left">Mes</th>
                @for ($day = 1; $day <= 31; $day++)
                    <th class="border px-1 py-1 text-center w-6">{{ $day }}</th>
                @endfor
            </tr>
            </thead>
            <tbody>
            @foreach($monthlyCalendar as $month => $days)
                <tr>
                    <td class="border px-2 py-1 font-bold">{{ ucfirst($month) }}</td>
                    @for ($day = 1; $day <= 31; $day++)
                        @php
                            $dayData = collect($days)->firstWhere('dia_numero', $day);
                            $isFestiu = $dayData['festiu'] ?? false;
                            $isNoLectiu = $dayData['noLectiu'] ?? false;
                            $color = isset($dayData['colaboracion_id']) && isset($colaboracionColors[$dayData['colaboracion_id']])
                                        ? "background-color: {$colaboracionColors[$dayData['colaboracion_id']]};"
                                        : '';
                        @endphp

                        @if ($dayData && !empty($dayData['id']))

                            <td style="padding:5px; {{ $color }} {{ $isFestiu ? 'background-color:#ffcccc;' : ( $isNoLectiu ? 'background-color:#fff2cc;' : '') }}"
                                class="border px-1 py-1 text-center w-6" title="{{ $resumColaboracions[$dayData['colaboracion_id']]['nom'] ?? '' }}"
                            >

                                    <span class="cursor-pointer"
                                          onclick="this.nextElementSibling.classList.remove('hidden'); this.classList.add('hidden')">
                                        {{ $dayData['hores_previstes'] > 0 ? number_format($dayData['hores_previstes'], 1) : '--' }}
                                    </span>

                                <input type="text" inputmode="decimal" pattern="[0-9]+([.,][0-9]{1,2})?" class="hidden border w-10 text-center text-xs bg-white text-black"
                                       value="{{ $dayData['hores_previstes'] }}"
                                       onchange="this.previousElementSibling.innerText = this.value;
                                                     this.classList.add('hidden');
                                                     this.previousElementSibling.classList.remove('hidden');"
                                       wire:change="updateDay({{ $dayData['id'] }}, $event.target.value)">
                            </td>
                        @elseif($dayData)
                            <td class="border px-1 py-1 text-center w-6">–</td>
                        @else
                            <td class="border px-1 py-1 text-center w-6">–</td>
                        @endif
                    @endfor
                </tr>
            @endforeach
            </tbody>
        </table>
        @if (count($resumColaboracions) > 0)

            <div class="mt-4">
                <h3 class="text-sm font-bold mb-2">Llegenda de col·laboracions:</h3>
                <ul class="text-xs">
                    @foreach($resumColaboracions as $id => $dades)
                        <li class="flex items-center gap-2">
                            <span class="inline-block w-4 h-4 rounded-sm border border-gray-400"
                                  style="background-color: {{ $dades['color'] }};">
                             {{ $dades['nom'] }} — <strong>{{ $dades['hores'] }} hores</strong></span>
                        </li>
                    @endforeach
                </ul>
            </div>

        @endif
        <div class="mt-4 text-sm font-bold">
            <span>Total d'hores previstes: </span>
            <span class="text-blue-600">{{ $totalHours }}</span>
        </div>
    </div>
@endif
</div>
