@if($showConfigForm)
    <div>

        <!-- ✅ AUTORITZACIONS -->
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



        <!-- ✅ TRAMS -->
        <div class="mb-6">
            <label class="block font-bold mb-2">Períodes de pràctiques</label>

            @foreach($trams as $index => $tram)
                <div class="border rounded p-3 mb-3 bg-gray-50 relative text-sm">
                    <p class="mb-2 font-semibold">Tram {{ $index + 1 }}</p>

                    <label class="block text-xs font-bold">Inici:</label>
                    <input type="date" wire:model="trams.{{ $index }}.inici" class="border p-1 w-full text-xs mb-1">
                    @error("trams.$index.inici") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

                    <label class="block text-xs font-bold">Fi:</label>
                    <input type="date" wire:model="trams.{{ $index }}.fi" class="border p-1 w-full text-xs mb-1">
                    @error("trams.$index.fi") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

                    <label class="block text-xs font-bold">Hores totals:</label>
                    <input type="number" wire:model="trams.{{ $index }}.hores" min="1" class="border p-1 w-full text-xs">
                    @error("trams.$index.hores") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

                    <label class="block text-xs font-bold mt-3">Hores per dia</label>
                    <table class="table-auto border-collapse border w-full text-xs">
                        <thead class="bg-gray-100">
                        <tr>
                            @foreach($tram['horesPerDia'] as $dia => $valor)
                                <th class="border px-2 py-1 text-center">{{ $dia }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @foreach($tram['horesPerDia'] as $dia => $valor)
                                <td class="border px-1 py-1 text-center">
                                    <input type="number" wire:model="trams.{{ $index }}.horesPerDia.{{ $dia }}" class="w-12 border text-center text-xs" min="0" max="24">
                                </td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>

                    <button type="button" class="absolute top-1 right-2 text-red-600 text-xs" wire:click="eliminarTram({{ $index }})">🗑</button>
                </div>
            @endforeach

            <button type="button" wire:click="afegirTram" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                + Afegeix Tram
            </button>
        </div>

        <!-- ✅ GENERAR -->
        <button wire:click="createCalendar" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Generar Calendari
        </button>

        <!-- ✅ HORARI -->
        <div class="mt-4 text-sm">
            <p><strong>Horari en la intranet:</strong> {{ $alumnoFct->Fct->Colaboracion->Centro->horarios ?? 'No especificat' }}</p>
        </div>

    </div>
@else
    <div>
        <h2 class="text-lg font-bold mb-4">Calendari de FCT</h2>

        <!-- ✅ TRAMS VISUALS -->
        <div class="mb-6">
            <h3 class="text-md font-bold mb-2">📋 Trams definits</h3>
            @foreach($trams as $index => $tram)
                <div class="border rounded p-3 mb-3 bg-gray-50 text-sm">
                    <p>
                        <strong>Tram {{ $index + 1 }}:</strong>
                        del <span class="text-blue-600">{{ \Carbon\Carbon::parse($tram['inici'])->format('d/m/Y') }}</span>
                        al <span class="text-blue-600">{{ \Carbon\Carbon::parse($tram['fi'])->format('d/m/Y') }}</span> –
                        <strong>{{ $tram['hores'] }} hores</strong>
                    </p>

                    <table class="table-auto text-xs mt-2 border-collapse border w-full">
                        <thead class="bg-gray-100">
                        <tr>
                            @foreach($tram['horesPerDia'] as $dia => $valor)
                                <th class="border px-2 py-1 text-center">{{ $dia }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @foreach($tram['horesPerDia'] as $dia => $valor)
                                <td class="border px-2 py-1 text-center">{{ $valor }}</td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

        <!-- ✅ ACCIONS -->
        <div class="mt-4 flex gap-3">
            <button wire:click="deleteCalendar" class="bg-red-500 text-white px-4 py-2 rounded">
                🗑 Esborrar Calendari
            </button>
            <button wire:click="exportCalendarPdf" class="bg-green-600 text-white px-4 py-2 rounded">
                📄 Descarregar PDF
            </button>
        </div>

        <!-- ✅ TAULA -->
        <table class="table-auto border-collapse border w-full text-xs mt-6">
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
                    <td class="border px-2 py-1 font-bold">{{ $month }}</td>
                    @for ($day = 1; $day <= 31; $day++)
                        @php
                            $dayData = collect($days)->firstWhere('dia_numero', $day);
                            $isFestiu = $dayData['festiu'] ?? false;
                            $isLectiu = $dayData['lectiu'] ?? false;
                        @endphp

                        @if ($dayData)
                            <td class="border px-1 py-1 text-center w-6 {{ $isFestiu ? 'bg-danger' : (!$isLectiu ? 'bg-warning' :'bg-info') }}">
                                    <span class="cursor-pointer"
                                          onclick="this.nextElementSibling.classList.remove('hidden'); this.classList.add('hidden')">
                                        {{ number_format($dayData['hores_previstes'], 1) }}
                                    </span>
                                <input type="number" class="hidden border w-10 text-center text-xs bg-white text-black"
                                       value="{{ $dayData['hores_previstes'] }}"
                                       onchange="this.previousElementSibling.innerText = this.value;
                                                     this.classList.add('hidden');
                                                     this.previousElementSibling.classList.remove('hidden');"
                                       wire:change="updateDay({{ $dayData['id'] }}, $event.target.value)">
                            </td>
                        @else
                            <td class="border px-1 py-1 text-center w-6">–</td>
                        @endif
                    @endfor
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- ✅ TOTAL -->
        <div class="mt-4 text-sm font-bold">
            <span>Total d'hores previstes: </span>
            <span class="text-blue-600">{{ $totalHours }}</span>
        </div>

        <!-- ✅ AFEGIR DIES -->
        <div class="mt-4">
            <h3 class="text-lg font-bold">Afegir dies</h3>
            <input type="number" wire:model="daysToAdd" class="border px-2 py-1 w-20" min="1">
            <button wire:click="addDays" class="bg-green-500 text-white px-2 py-1 rounded ml-2">
                ➕ Afegir Dies
            </button>
        </div>
    </div>
@endif
