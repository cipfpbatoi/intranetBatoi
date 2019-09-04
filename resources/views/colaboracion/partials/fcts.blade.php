<ul class="messages colaboracion">
    @foreach ($fcts as $fct)
        <a href="/fct/{{$fct->id}}/show"><i class="fa fa-eye"></i> {{$fct->Instructor->Nombre}}</a>
        <i id='{{$fct->id}}' class="fa fa-plus" data-toggle="modal" data-target="#AddAlumno"> @lang("messages.generic.anadir") @lang("models.modelos.Alumno")</i>
        @foreach ($fct->Alumnos as $alumno)
            <li>
                <div class="message_wrapper">
                    <h5>
                        <i class="fa fa-calendar user-profile-icon"></i> {!! $alumno->pivot->desde !!} {{ $alumno->pivot->hasta }}
                        <i class="fa fa-user user-profile-icon"></i> {{ $alumno->fullName }}
                    </h5>
                </div>
            </li>
        @endforeach
    @endforeach
    @if (count($fcts)) @include('colaboracion.partials.modalAlumnos')
    @else
        <i class="fa fa-plus" data-toggle="modal" data-target="#AddAlumno"> @lang("messages.generic.anadir") @lang("models.modelos.Alumno")</i>
        @include('colaboracion.partials.modalInstructor')
    @endif
</ul>