<div>
    <h2 class="text-lg font-bold mb-4">Calendari de FCT</h2>

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
                            <input type="number" class="hidden border w-10 text-center text-xs"
                                   value="{{ $dayData['hores_previstes'] }}"
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
        <h3 class="text-lg font-bold">Configurar hores per dia de la setmana</h3>
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
                            <span class="cursor-pointer"
                                  onclick="this.nextElementSibling.classList.remove('hidden'); this.classList.add('hidden')">
                                {{ $hours }}
                            </span>
                        <input type="number" class="hidden border w-12 text-center text-xs"
                               value="{{ $hours }}"
                               onchange="this.previousElementSibling.innerText = this.value;
                                             this.classList.add('hidden');
                                             this.previousElementSibling.classList.remove('hidden');"
                               wire:change="updateDefaultHours('{{ $day }}', $event.target.value)">
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
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
