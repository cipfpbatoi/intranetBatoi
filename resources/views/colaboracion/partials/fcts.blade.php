<ul class="messages colaboracion">
    @foreach ($fcts??[] as $fct)
        <em class="fa fa-credit-card"></em> {{$fct->Instructor->id??'No hi ha instructor'}}
        <em class="fa fa-user user-profile-icon"></em> {{$fct->Instructor->Nombre??''}}
        <em class="fa fa-envelope"></em> {{$fct->Instructor->email??''}}
        <em class="fa fa-phone"></em> {{$fct->Instructor->telefono??''}}
        <em id='{{$fct->id}}' class="fa fa-plus" data-toggle="modal" data-target="#AddAlumno">
            @lang("messages.generic.anadir") @lang("models.modelos.Alumno")
        </em>
        @foreach ($fct->Alumnos??[] as $alumno)
            <li>
                <div class="message_wrapper">
                    <h5>
                        <em class="fa fa-calendar user-profile-icon"></em>
                            {!! $alumno->pivot->desde !!} {{ $alumno->pivot->hasta }}
                        <em class="fa fa-user user-profile-icon"></em>
                            {{ $alumno->fullName }}
                    </h5>
                </div>
            </li>
        @endforeach
    @endforeach
    @if (count($fcts)) @include('colaboracion.partials.modalAlumnos')
    @else
        <em class="fa fa-plus" data-toggle="modal" data-target="#AddAlumno">
            @lang("messages.generic.anadir") @lang("models.modelos.Alumno")
        </em>
        @include('colaboracion.partials.modalInstructor')
    @endif
</ul>
