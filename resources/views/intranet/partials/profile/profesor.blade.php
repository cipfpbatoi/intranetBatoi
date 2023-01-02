@foreach ($panel->getElementos($pestana) as $elemento)
    <x-label
            id="{{$elemento->dni}}"
            cab1="{{ $elemento->FullName }}"
            cab2="{{ $elemento->Departamento->literal}}"
            title="{{ asset('storage/'.$elemento->foto) }}"
            view="people"
    >
            @if (isset(authUser()->codigo))
                <li><em class="fa fa-phone"></em>
                    @if ($elemento->mostrar)
                        {{$elemento->movil1}}
                    @else
                        -oculto-
                    @endif
                </li>
                @if ($elemento->mostrar)
                    <li><em class="fa fa-phone"></em>{{$elemento->movil2}}</li>
                @endif
            @endif
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
                @if ($elemento->ahora)
                    <a href='#' class='btn btn-primary btn-xs'>{{$elemento->ahora}}</a>
                @endif
            </x-slot>
    </x-label>
@endforeach
