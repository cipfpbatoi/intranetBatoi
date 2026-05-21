<ul class="messages">
    @foreach ($faltas as $falta)
        @if ($falta->profesor->activo)
            @php
                $image = 'ill.png';
                $motiu = mb_strtolower((string) $falta->motivo);
                $esMalaltia = str_contains($motiu, 'enfermedad')
                    || str_contains($motiu, 'malaltia')
                    || str_contains($motiu, 'medico');

                if (!$esMalaltia) {
                    $image = 'actividad.png';
                }
            @endphp
            <x-llist image="{{ $image }}" date="{{ $falta->dia_completo ? 'HUI' : $falta->hora_ini . ' - ' . $falta->hora_fin }}">
                <h4 class="heading">
                    {{ $falta->profesor->fullName }}
                    @if ((int) $falta->estado === 0 && authUser() && esRol(authUser()->rol, config('roles.rol.direccion')))
                        <span class="text-danger"> -</span>
                    @endif
                </h4>
                <br />
            </x-llist>
        @endif
    @endforeach

    @foreach ($hoyActividades as $actividad)
        @foreach ($actividad->profesores as $profesor)
            <x-llist image="actividad.png" date="{{ hora($actividad->desde) . ' - ' . hora($actividad->hasta) }}">
                <h4 class="heading">{{ $profesor->fullName }}</h4>
                <br />
            </x-llist>
        @endforeach
    @endforeach

    @foreach ($comisiones as $comision)
        <x-llist image="coche.png" date="{{ hora($comision->desde) . ' - ' . hora($comision->hasta) }}">
            <h4 class="heading">{{ $comision->profesor->fullName }}</h4>
            <br />
        </x-llist>
    @endforeach
</ul>
