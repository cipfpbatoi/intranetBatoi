<ul class="messages fct">
    @foreach($fct->alFct as $alfct)
        @php
            $alumno = $alfct->Alumno;
            $mio = in_array(authUser(),$alumno->Tutor)
        @endphp
        @if ($mio)
            <li>
                <div class="message_date">

                    <h4 class="text-info"><i
                                class="fa fa-calendar-times-o"></i> {{$alfct->desde}}
                        - {{$alfct->hasta}} ({{$alfct->horas}})</h4>
                    <h4 class="text-info">
                        Tutor:
                        @foreach ($alumno->Tutor as $tutor)
                            {{$tutor->FullName}}
                        @endforeach
                    </h4>
                    <h4>
                        @if ($alfct->idSao)
                            <em class="fa fa-link"> SAO</em>
                        @endif
                        @if ($alfct->saoAnnexes)
                            <em class="fa fa-file-pdf-o"> A2 A3</em>
                        @endif
                    </h4>
                </div>
                <div class="message_wrapper">
                    <h4 class="text-info"><i class="fa fa-user"></i>{{$alumno->FullName}}</h4>
                    <h4 class="text-info">
                        <i class="fa fa-phone"></i> {{$alumno->telef1}}
                        <i class="fa fa-envelope"></i> {{$alumno->email}}
                    </h4>
                    <a href="{{ route('alumnofct.pdf',$alfct->id) }}" class="fa fa-file-pdf-o" target="_blank">
                        Cert.Alu.
                    </a>
                    @if ($alfct->autorizacion)
                        <a href="{{ route('alumnofct.auth',$alfct->id) }}" class="fa fa-file-zip-o" target="_blank">
                            Autorització
                        </a>
                    @endif
                    @if ($fct->asociacion == '2')
                        <a href="{{ route('alumnofct.A1',$alfct->id) }}" class="fa fa-file-zip-o" target="_blank">
                            Annexos Anglès
                        </a>
                    @endif
                    <a href="{{ route('alumnofct.A5',$alfct->id) }}" class="fa fa-file-pdf-o" target="_blank">
                        Inf.Valoratiu
                    </a>
                </div>
            </li>
        @else
            <li>
                <div class="message_date">
                    <p class="text-info">
                        Tutor:
                        @foreach ($alumno->Tutor as $tutor)
                            {{$tutor->FullName}}
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
