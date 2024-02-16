@foreach ($panel->getElementos($pestana) as $elemento)
    <x-label
            id="{{$elemento->id}}"
            cab1="{{$elemento->desde}}"
            cab2="{{  esMismoDia($elemento->desde,$elemento->hasta)?
                substr($elemento->hasta,11):
                $elemento->hasta }}"
            title="{{$elemento->name}}"
        >
        <p><strong>Descripció</strong> : <em style="font-size: smaller">{{$elemento->descripcion}}</em></p>
        @if ($elemento->objetivos)
            <p><strong>Objectius</strong> : <em style="font-size: smaller">{{$elemento->objetivos}}</em></p>
        @endif
        @if ($elemento->comentarios)
            <p><strong>Comentaris</strong> : <em style="font-size: smaller">{{$elemento->comentarios}}</em></p>
        @endif
        <h5>Participants</h5>
        <ul class="list-unstyled">
            @foreach ($elemento->profesores as $profesor)
                <li><em class="fa fa-user"></em>
                    @if($profesor->pivot->coordinador)
                        <strong>{{$profesor->nombre}} {{$profesor->apellido1}}</strong>
                    @else
                        {{$profesor->nombre}} {{$profesor->apellido1}}
                    @endif
                </li>
            @endforeach
            @foreach ($elemento->grupos as $grupo)
                <li><em class="fa fa-group"></em> {{ $grupo->nombre}} </li>
            @endforeach
        </ul>
        <x-slot name="rattings">
            @if ($elemento->estraescolar == 1)
                <a href='#' class='btn btn-success btn-xs' >@lang("messages.menu.Orientacion")</a>
            @else
                @if ($elemento->estado<2)
                    <a href='#' class='btn btn-danger btn-xs' >
                @else
                    <a href='#' class='btn btn-success btn-xs' >
                @endif
                {{ $elemento->situacion }}</a>
            @endif
            @if ($elemento->fueraCentro)
                <a href='#' class='btn btn-info btn-xs' >Extraescolar</a>
            @else
                <a href='#' class='btn btn-info btn-xs' >Centre</a>
            @endif
            @if ($elemento->transport)
                    <a href='#' class='btn btn-warning btn-xs' >Transport</a>
            @endif
        </x-slot>
        <x-slot name="botones">
            @foreach($panel->getBotones('profile') as $button)
                {{ $button->show($elemento) }}
            @endforeach
        </x-slot>
    </x-label>
@endforeach
