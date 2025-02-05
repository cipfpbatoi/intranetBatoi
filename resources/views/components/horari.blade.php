<div>
    <table class='table table-bordered'>
        @include('home.partials.horario.cabeceraTabla')
        <tbody>
        @foreach (Intranet\Entities\Hora::all() as $hora)
            @php
                // Convertim l'hora d'inici a minuts per comparar-la (per la separació de matí/vesprada)
                $hora_ini = strtotime($hora->hora_ini);
                $limit_vesprada = strtotime('14:55');
                $hasActivity = collect(['L', 'M', 'X', 'J', 'V'])->some(fn($dia) => isset($horario[$dia][$hora->codigo]));
            @endphp

            {{-- Afegir una fila separadora per a la vesprada --}}
            @if ($hora_ini == $limit_vesprada)
                <tr>
                    <td colspan="6" class="text-center bg-info"><strong>@lang('messages.generic.afternoon')</strong></td>
                </tr>
            @endif

            @if ($hasActivity)
                <tr>
                    <td>{{$hora->hora_ini}} - {{$hora->hora_fin}}</td>
                    @foreach (['L', 'M', 'X', 'J', 'V'] as $dia_semana)
                        @php $celda = $horario[$dia_semana][$hora->codigo] ?? null; @endphp
                        <td class="
                            @if ($celda)
                                {{ isset($celda->Modulo) ? 'active' : 'warning' }}
                            @endif
                        ">
                            @if ($celda)
                                {{-- Si no hi ha mòdul, mostrar ocupació en color crema i sense parèntesi d'aula --}}
                                @if (!isset($celda->Modulo))
                                    <strong>{{ $celda->Ocupacion->nombre ?? 'Ocupació desconeguda' }}</strong>
                                @else
                                    {{-- Mostrar mòdul només si existeix --}}
                                    @if ($config['mostrar_modulo'] ?? false)
                                        <strong>{{ $celda->Modulo->cliteral }}</strong>
                                    @endif
                                    {{-- Mostrar aula només per als mòduls --}}
                                    @if ($config['mostrar_aula'] ?? false)
                                        ({{ $celda->aula }})
                                    @endif
                                @endif

                                {{-- Mostrar professor si està habilitat --}}
                                @if ($config['mostrar_professor'] ?? false)
                                    <br/>{{ $celda->Mestre->FullName ?? '' }}
                                @endif

                                {{-- Mostrar grup si està habilitat --}}
                                @if ($config['mostrar_grup'] ?? false)
                                    <br/>{{ $celda->Grupo->nombre ?? '' }}
                                @endif
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>


</div>