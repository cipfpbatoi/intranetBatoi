@foreach ($panel->getElementos($pestana)->sortBy('subGrupo')->sortBy('posicion') as $elemento)
    @isset($elemento->Alumno)
        @php($alumno = $elemento->Alumno)
        <x-label
                id="{{$alumno->nia}}"
                cab1="{{ $alumno->nia }}"
                cab2="{{ $alumno->dni }}"
                title="{{ asset('storage/'.$alumno->foto) }}"
                subtitle="{{ $alumno->fullName }}"
                view="alumno"
        >
            <li><em class="fa fa-building"></em> {{$alumno->domicilio}} </li>
            <li>
                <em class="fa fa-envelope-o"></em>
                {{$alumno->codigo_postal}}
                @isset($alumno->Municipio){{$alumno->Municipio->municipio}}@endisset
            </li>
            <li><em class="fa fa-envelope"></em> {{$elemento->email}}</li>
            <x-slot name="rattings">
                    <a>{{ $alumno->edat }} {{ trans("validation.attributes.años") }}</a>
                    @if ($alumno->repite == 0)
                        <a href="#"><span class="fa fa-star-o"></span></a>
                    @else
                        <a href="#"><span class="fa fa-star"></span></a>
                    @endif
            </x-slot>
            <x-slot name="botones">
                @foreach ($panel->getBotones('profile') as $button)
                    {{ $button->show($elemento) }}
                @endforeach
            </x-slot>
        </x-label>
    @endisset
@endforeach

