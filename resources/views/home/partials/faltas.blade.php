<ul class="messages">
    @foreach ($faltas as $falta)
            @if ($falta->profesor->activo)
                <li>
                <img src="/img/ill.png" class="avatar" alt="Avatar">
                <div class="message_date">
                    @if ($falta->dia_completo)
                        HUI
                    @else
                        {{$falta->hora_ini}} - {{$falta->hora_fin}}
                    @endif
                </div>
                <div class="message_wrapper">
                    <h4 class="heading">{{$falta->profesor->fullName}}</h4>
                    <br />
                </div>
                </li>
            @endif
    @endforeach
    @foreach ($hoyActividades as $actividad)
        @foreach ($actividad->profesores as $profesor)
                <li>
                    <img src="/img/actividad.png" class="avatar" alt="Avatar">
                    <div class="message_date">
                        {{hora($actividad->desde)}} - {{hora($actividad->hasta)}}
                    </div>
                    <div class="message_wrapper">
                        <h4 class="heading">{{$profesor->fullName}}</h4>
                        <br />
                    </div>
                </li>
            @endforeach
        @endforeach
        @foreach ($comisiones as $comision)
            <li>
                <img src="/img/coche.png" class="avatar" alt="Avatar">
                <div class="message_date">
                    {{hora($comision->desde)}} - {{hora($comision->hasta)}}
                </div>
                <div class="message_wrapper">
                    <h4 class="heading">{{$comision->profesor->fullName}}</h4>
                    <br />
                </div>
            </li>
    @endforeach
</ul>