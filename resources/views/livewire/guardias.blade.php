<div>
    <div>
        <button wire:click="weekBefore"> << {{$firstDay->format('d-m-Y')}}</button>
        <button wire:click="weekAfter">{{$lastDay->format('d-m-Y')}} >></button>
    </div>
    <table class="table custom-table">
        <thead >
        <tr>
            <th style="width: 10%">Hora</th>
            @foreach ($dias as $key => $dia)
                <th class="px-6 py-3 text-left" style="width: 18%">
                    <div class="flext items-center">
                        <span>{{$dia}}</span>
                    </div>
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach ($horas as $hora)
            <tr>
                    <td>
                        {{ $hora->hora_ini }} -  {{ $hora->hora_fin }}
                    </td>
                    @foreach ($dias as $dia )
                        <th class="px-6 py-3 text-left">
                            <div class="flext items-center">
                                @isset($registers[$hora->codigo][$dia])
                                    @foreach ($registers[$hora->codigo][$dia] as $register)
                                        <span class="label {{ $register->realizada==-1?'label-danger':($register->realizada?'label-default':'label-warning') }}">
                                            {{$register->Profesor->shortName}}
                                        </span>
                                        @isset($register->observaciones) <small>{{$register->observaciones}}</small>@endisset
                                        @isset($register->obs_personal) <small>{{$register->obs_personal}}</small>@endisset
                                        <br/>
                                    @endforeach
                                @endisset
                            </div>
                        </th>
                    @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
