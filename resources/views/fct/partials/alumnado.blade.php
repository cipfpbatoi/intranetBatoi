<ul class="messages colaboracion">
    @foreach ($fct->alFct as $alumnoFct)
        @foreach ($alumnoFct->Contactos as $contacto)
            <li>
                <div class="message_wrapper">
                    <h5>
                        <em class="fa fa-calendar user-profile-icon"></em> {!! $contacto->created_at !!}
                        <em class="fa fa-envelope user-profile-icon"></em> {{$contacto->document}}
                        <em class="fa fa-user user-profile-icon"></em> {{$alumnoFct->Alumno->fullName??''}}
                    </h5>
                </div>
            </li>
        @endforeach
    @endforeach
</ul>