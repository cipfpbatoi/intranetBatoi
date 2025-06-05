<ul class="messages fct">
    @foreach($fct->alFct as $alfct)
        @php
            $alumno = $alfct->Alumno;
            $mio = in_array(authUser()->dni,$alfct->Tutor->Sustituidos);
        @endphp
        @if ($mio)
            <li>
                <div class="message_date">

                    <h4 class="text-info"><i
                                class="fa fa-calendar-times-o"></i> {{$alfct->desde}}
                        - {{$alfct->hasta}} ({{$alfct->horas}})</h4>
                    <h4 class="text-info">
                        Tutor: {{$alfct->Tutor->fullName??''}}

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
                    @if ($alfct->autorizacion)
                        <a href="{{ route('alumnofct.auth',$alfct->id) }}" class="fa fa-file-zip-o" target="_blank">
                            Autorització
                        </a>
                    @endif
                    @if ($alfct->fct->asociacion == 4)
                        <a href="{{ route('alumnofct.AVI',$alfct->id) }}" class="fa fa-file-pdf-o" target="_blank">
                            Conformitat Alumnat
                        </a>
                    @else
                        <a href="{{ route('alumnofct.AEng',$alfct->id) }}" class="fa fa-file-zip-o" target="_blank">
                            Annexos Anglès
                        </a>
                    @endif
                    @if ($alfct->fct->asociacion == 4)
                        <a href="{{ route('alumnofct.AutDual',$alfct->id) }}" class="fa fa-file-pdf-o" target="_blank">
                            Autorització No Lectius
                        </a>
                    @else
                        <a href="{{ route('alumnofct.Valoratiu',$alfct->id) }}" class="fa fa-file-pdf-o" target="_blank">
                            Inf.Competències Adquirides
                        </a>
                    @endif

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

