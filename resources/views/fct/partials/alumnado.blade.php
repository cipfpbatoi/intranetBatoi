<ul class="messages colaboracion" id="ul_llist">
    @foreach ($fct->alFct as $alumnoFct)
        @foreach ($alumnoFct->Contactos as $contacto)
            <li>
                <div class="message_wrapper">
                    <h5>
                        <em class="fa fa-calendar user-profile-icon"></em> {!! $contacto->created_at !!}
                        @if ($contacto->document == 'email')
                            <em class="fa fa-envelope"></em>
                        @else
                            <em class="fa fa-exclamation"></em>
                        @endif
                        {{$contacto->document}}
                        <em class="fa fa-user user-profile-icon"></em> {{$alumnoFct->Alumno->fullName??''}}
                        @if ($contacto->comentari) <br/>{{ $contacto->comentari }} @endif
                    </h5>
                </div>
            </li>
        @endforeach
    @endforeach
</ul>
<a class="alumnat"><em class="fa fa-plus"></em>Afegir seguiment</a>
