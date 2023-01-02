@foreach ($panel->getElementos($pestana) as $elemento)
    <x-label
        id="{{$elemento->id}}"
        cab1="{{$elemento->desde}}"
        cab2="{{  esMismoDia($elemento->desde,$elemento->hasta)?
                substr($elemento->hasta,11):
                $elemento->hasta }}"
        title="{{($elemento->fct)?'FCT':''}}
            {{$elemento->Profesor->nombre.' '.$elemento->Profesor->apellido1}}"
        subtitle="{{$elemento->descripcion}}">
        <ul>
            <li>
                <em class="fa fa-automobile"></em> {{$elemento->tipoVehiculo}} - {{$elemento->kilometraje}} km.
            </li>
            @isset($elemento->marca)
                <li>
                    <em class="fa fa-automobile"></em> {{ $elemento->marca}} {{$elemento->matricula}}
                </li>
            @endisset
            <li>
                <em class="fa fa-money"></em> {{ $elemento->total }}
            </li>
        </ul>
        <x-slot name="rattings">
            <a href='#' class='btn {{$elemento->estado<2?'btn-danger':'btn-success'}} btn-xs'>
                {{ $elemento->situacion }}
            </a>
        </x-slot>
        <x-slot name="botones">
            @foreach($panel->getBotones('profile') as $button)
                {{ $button->show($elemento) }}
            @endforeach
        </x-slot>
    </x-label>
@endforeach

