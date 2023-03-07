<ul class="messages fct">
    @foreach($fct->Alumnos as $alumno)
        @php $mio = in_array(authUser(),$alumno->Tutor) @endphp
        @if ($mio)
            <li>
                <div class="message_date">
                    <h4 class="text-info">Tutor: - @foreach ($alumno->Tutor as $tutor)
                            {{$tutor->FullName}} -
                        @endforeach</h4>
                    <h4 class="text-info"><i
                                class="fa fa-calendar-times-o user-profile-icon"></i>{{$alumno->pivot->desde}}
                        - {{$alumno->pivot->hasta}} ({{$alumno->pivot->horas}})</h4>
                </div>
                <div class="message_wrapper">
                    <h4 class="text-info">
                        <a href="/fct/{!!$fct->id!!}/{!!$alumno->nia!!}/alumnoDelete"><i
                                    class="fa fa-trash-o user-profile-icon"></i></a>
                        {{$alumno->FullName}}</h4>
                    <h4 class="text-info"><i class="fa fa-phone user-profile-icon"></i> {{$alumno->telef1}} <i
                                class="fa fa-envelope user-profile-icon"></i> {{$alumno->email}}</h4>
                </div>
            </li>
        @else
            <li>
                <div class="message_date">
                    <p class="text-info">Tutor: - @foreach ($alumno->Tutor as $tutor)
                            {{$tutor->FullName}} -
                        @endforeach</p>
                </div>
                <div class="message_wrapper">
                    <p class="text-info">{{$alumno->FullName}}</p>
                </div>
            </li>
        @endif
    @endforeach
</ul>
