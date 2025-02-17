@if($showConfigForm)
    <div>
        <div class="mt-4 flex items-center">
            <input type="checkbox" id="autorizacion" class="mr-2"
                   wire:model="allowNoLectiu">
            <label for="autorizacion" class="text-sm font-bold">Pot fer pràctiques en dies no lectius</label>
        </div>
        @if($allowNoLectiu)
            <div class="mt-4 flex items-center"  >
                <input type="checkbox" id="allowFestiu" class="mr-2"
                       wire:model="allowFestiu">
                <label for="allowFestiu" class="text-sm font-bold">Pot fer pràctiques en dies festius</label>
            </div>
        @endif

        <div class="mb-4">
            <label class="block font-bold">Defineix les hores per cada dia</label>
            <table class="table-auto border-collapse border w-full text-xs">
                <thead>
                <tr class="bg-gray-200">
                    @foreach($defaultHours as $day => $hours)
                        <th class="border px-2 py-1 text-center">{{ $day }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr>
                    @foreach($defaultHours as $day => $hours)
                        <td class="border px-2 py-1 text-center">
                            <input type="number" wire:model="defaultHours.{{ $day }}" class="border w-12 text-center text-xs" min="0" max="24">
                        </td>
                    @endforeach
                </tr>
                </tbody>
            </table>
            <button wire:click="createCalendar"
                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
                Generar Calendari
            </button>
        </div>
        <div class="mb-4">
            <p><strong>Horari en la intranet:</strong>  {{ $alumnoFct->Fct->Colaboracion->Centro->horarios ?? 'No especificat'}} </p>
        </div>

    </div>
@else
<div>
    <h2 class="text-lg font-bold mb-4">Calendari de FCT</h2>
    <button wire:click="deleteCalendar"
            class="mb-4 bg-red-500 text-white px-4 py-2 rounded">
        Esborrar Calendari
    </button>

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
                <td class="border px-2 py-1 font-bold">{{ $month }}</td>
                @for ($day = 1; $day <= 31; $day++)
                    @php
                        $dayData = collect($days)->firstWhere('dia_numero', $day);
                        $isWeekend = $dayData['weekend'] ?? false;
                    @endphp
                    <td style="padding:5px" class="border px-1 py-1 text-center w-6
                            {{ $isWeekend ? 'bg-red' : 'bg-blue' }}">
                        @if ($dayData)
                            <span class="cursor-pointer"
                                  onclick="this.nextElementSibling.classList.remove('hidden'); this.classList.add('hidden')">
                                    {{ $dayData['hores_previstes'] }}
                                </span>
                            <input type="text" class="hidden border w-10 text-center text-xs bg-white text-black"
                                   value="{{ $dayData['hores_previstes'] }}"
                                   oninput="validateNumberInput(this)"
                                   onchange="this.previousElementSibling.innerText = this.value;
                                                 this.classList.add('hidden');
                                                 this.previousElementSibling.classList.remove('hidden');"
                                   wire:change="updateDay({{ $dayData['id'] }}, $event.target.value)">
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-4 text-sm font-bold">
        <span>Total d'hores previstes: </span>
        <span class="text-blue-600">{{ $totalHours }}</span>
    </div>


    <div class="mt-4">
        <h3 class="text-lg font-bold">Afegir dies</h3>
        <input type="number" wire:model="daysToAdd" class="border px-2 py-1 w-20" min="1">
        <button wire:click="addDays"
                class="bg-green-500 text-white px-2 py-1 rounded">
            Afegir Dies
        </button>
    </div>
</div>
@endif
<script>
    function validateNumberInput(input) {
        let value = input.value.replace(/[^0-9]/g, ''); // Elimina lletres i símbols
        value = Math.max(0, Math.min(12, parseInt(value) || 0)); // Limita de 0 a 24
        input.value = value;
    }
</script>
