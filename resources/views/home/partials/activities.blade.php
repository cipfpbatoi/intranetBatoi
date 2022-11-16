<ul class="messages">
    <li>
        @foreach ($actividades as $actividad)
            <img src="/img/actividad.png" class="avatar" alt="Avatar">
            <div class="message_date">
                <h3 class="date text-info">{{day($actividad->desde)}}</h3>
                <p class="month">{{month($actividad->desde)}}</p>
            </div>
            <div class="message_wrapper">
                @if ($actividad->Tutor->first()!==null)
                    <h4 class="heading">{{$actividad->Tutor->first()->nombre}} {{$actividad->Tutor->first()->apellido1}}</h4>
                @else
                    <h4 class="heading">{{$actividad->profesores->first()->nombre}} {{$actividad->profesores->first()->apellido1}}</h4>
                @endif
                <blockquote class="message">{{$actividad->name}}</blockquote>
                <br/>
                <p class="url">
                    <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                    @if (!isset(authUser()->nia))
                        <a href="actividad/{{$actividad->id}}">@endif
                            <i class="fa fa-paperclip"></i>
                            @foreach ($actividad->grupos as $grupo)
                                {{ $grupo->nombre }} <i class="fa fa-paperclip"></i>
                            @endforeach
                            @if (!isset(authUser()->nia))</a>
                    @endif
                </p>
            </div>
        @endforeach
    </li>
</ul>