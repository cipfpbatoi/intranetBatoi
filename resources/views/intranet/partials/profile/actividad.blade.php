@foreach ($panel->getElementos($pestana) as $elemento)
    <x-label
            id="{{$elemento->id}}"
            cab1="{{$elemento->desde}}"
            cab2="{{  esMismoDia($elemento->desde,$elemento->hasta)?
                substr($elemento->hasta,11):
                $elemento->hasta }}"
            title="{{$elemento->name}}"
        >
        <p><strong>Descripció</strong> : {{$elemento->descripcion}}</p>
        <p><strong>Objectius</strong> : {{$elemento->objetivos}}</p>
        <h5>Participants</h5>
        <ul class="list-unstyled">
            @foreach ($elemento->profesores as $profesor)
                <li><em class="fa fa-user"></em> {{$profesor->nombre}} {{$profesor->apellido1}}</li>
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
        </x-slot>
        <x-slot name="botones">
            @foreach($panel->getBotones('profile') as $button)
                {{ $button->show($elemento) }}
            @endforeach
        </x-slot>
    </x-label>
@endforeach
