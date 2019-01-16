<ul class="messages">
    <li>
        @foreach ($faltas as $falta)
            @if ($falta->profesor->activo)
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
            @endif
        @endforeach
        @foreach ($hoyActividades as $actividad)
            @foreach ($actividad->profesores as $profesor) 
                <img src="/img/actividad.png" class="avatar" alt="Avatar">
                <div class="message_date">
                    {{hora($actividad->desde)}} - {{hora($actividad->hasta)}}
                </div>
                <div class="message_wrapper">
                    <h4 class="heading">{{$profesor->fullName}}</h4>
                    <br />
                </div>
            @endforeach
        @endforeach
        @foreach ($comisiones as $comision)
            <img src="/img/coche.png" class="avatar" alt="Avatar">
            <div class="message_date">
                {{hora($comision->desde)}} - {{hora($comision->hasta)}}
            </div>
            <div class="message_wrapper">
                <h4 class="heading">{{$comision->profesor->fullName}}</h4>
                <br />
            </div>
        @endforeach
    </li>
</ul>