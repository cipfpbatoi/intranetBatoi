<div class="border rounded-lg p-2 bg-gray-100 cursor-pointer" wire:click="seleccionarMes({{ $mes }})">
    <h4 class="text-sm font-semibold text-center mb-1">
        {{ \Carbon\Carbon::create($any, $mes, 1)->translatedFormat('F') }}
    </h4>

    <div class="grid grid-cols-7 gap-1">
        @foreach($dies[$mes] as $dia => $info)
            <div class="w-6 h-6 rounded"
                 style="background-color:
                    {{ $info['tipus'] == 'festiu' ? '#ffadad' :
                       ($info['tipus'] == 'no lectiu' ? '#ffffff' : '#caffbf') }};">
            </div>
        @endforeach
    </div>
</div>
