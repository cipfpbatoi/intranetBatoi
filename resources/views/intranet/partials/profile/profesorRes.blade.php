@foreach ($panel->getElementos($pestana) as $elemento)
    <x-label
            id="{{$elemento->dni}}"
            cab1="{{ $elemento->FullName }}"
            cab2="{{ $elemento->Departamento->literal}}"
            title="{{ asset('storage/fotos/'.$elemento->foto) }}"
            view="people"
    >
        <li><em class="fa fa-envelope"></em> {{$elemento->email}}</li>
        <x-slot name="rattings">
            @if (estaDentro($elemento->dni))
                {!! Html::image('img/clock-icon.png',
                        'reloj',array('class' => 'iconopequeno', 'id' => 'imgFitxar')) !!}
            @else
                {!! Html::image('img/clock-icon-rojo.png',
                        'reloj',array('class' => 'iconopequeno', 'id' => 'imgFitxar')) !!}
            @endif
        </x-slot>
        <x-slot name="botones">
            @foreach ($panel->getBotones('profile') as $button)
                {{ $button->show($elemento) }}
            @endforeach
            <a href='#' class='btn btn-primary btn-xs' title='{{$elemento->momento}}'>{{$elemento->ahora}}</a>
        </x-slot>
    </x-label>
@endforeach
