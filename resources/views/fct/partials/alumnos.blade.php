<ul class="messages fct">
    @php
        $userDni = (string) (authUser()->dni ?? '');
        $professorsAccessibles = $userDni !== ''
            ? \Intranet\Entities\Profesor::getSubstituts($userDni)
            : [];
    @endphp
    @foreach($fct->alFct as $alfct)
        @php
            $alumno = $alfct->Alumno;
            $tutorFct = $alfct->Tutor;
            $mio = in_array((string) ($alfct->idProfesor ?? ''), $professorsAccessibles, true);
        @endphp
        @if ($mio)
            <li>
                <div class="message_date">

                    <h4 class="text-info"><i
                                class="fa fa-calendar-times-o"></i> {{$alfct->desde}}
                        - {{$alfct->hasta}} ({{$alfct->horas}})</h4>
                    <h4 class="text-info">
                        Tutor: {{$tutorFct?->fullName ?? ''}}

                    </h4>
                    <h4>
                        @if ($alfct->idSao)
                            <i class="fa fa-unlink"> SAO - </i>
                        @endif
                        @if ($alfct->saoAnnexes)<em>@endif
                             Fitxers Annexes
                        @if ($alfct->saoAnnexes)</em>@endif
                        <a href="{{route('alumnofct.link',$alfct->id)}}" class="fa fa-file-pdf-o" title="Enllaçar fitxers"></a>
                    </h4>
                </div>
                <div class="message_wrapper">
                    <h4 class="text-info"><i class="fa fa-user"></i>{{$alumno->FullName}}</h4>
                    <h4 class="text-info">
                        <i class="fa fa-phone"></i> {{$alumno->telef1}}
                        <i class="fa fa-envelope"></i> {{$alumno->email}}
                    </h4>
                    <a href="{{ route('alumnofct.pdf',$alfct->id) }}" class="fa fa-file-pdf-o" target="_blank">
                        @if ($alfct->correoAlumno)<strong> @endif
                        Cert.Alu.
                        @if ($alfct->correoAlumno)</strong> @endif
                    </a>
                    <a href="{{ route('alumnofct.AEng',$alfct->id) }}" class="fa fa-file-zip-o" target="_blank">
                        Annexos Anglès
                    </a>
                </div>
            </li>
        @else
            <li>
                <div class="message_date">
                    <p class="text-info">
                        Tutor:
                        @foreach ($alumno->Tutor as $tutor)
                            {{$tutor->FullName ?? 'Sense nom'}}
                        @endforeach
                    </p>
                </div>
                <div class="message_wrapper">
                    <p class="text-info">{{$alumno->FullName}}</p>
                </div>
            </li>
        @endif
    @endforeach
</ul>
