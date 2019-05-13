<ul class="messages colaboracion">
    @foreach ($fcts as $fct)
        @foreach ($fct->Alumnos as $alumno)
            <li>
                <div class="message_wrapper">
                    <h5>
                        <i class="fa fa-calendar user-profile-icon"></i> {!! $alumno->pivot->desde !!} {{ $alumno->pivot->hasta }}
                        <i class="fa fa-user user-profile-icon"></i> {{ $alumno->fullName }}
                        <i class="fa fa-user user-profile-icon"></i> {{$fct->Instructor->Nombre}}
                    </h5>
                </div>
            </li>
        @endforeach
    @endforeach
</ul>