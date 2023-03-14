<ul class="messages colaboracion">
    @foreach ($fct->Contactos as $contacto)
        <li>
            <div class="message_wrapper">
                <h5>
                    <i class="fa fa-calendar user-profile-icon"></i> {!! $contacto->created_at !!}
                    @if ($contacto->action == 'email')
                        <i class="fa fa-envelope user-profile-icon"></i> {{$contacto->document}}
                    @endif
                    @if ($contacto->action == 'phone')
                        <i class="fa fa-phone user-profile-icon"></i> {{$contacto->document}}
                    @endif
                    @if ($contacto->action == 'visita')
                        <i class="fa fa-car user-profile-icon"></i> {{$contacto->document}}
                    @endif
                    <i class="fa fa-user user-profile-icon"></i> {{$contacto->Propietario->fullName}}
                </h5>
                @if ($contacto->comentari)
                    <h6>{{$contacto->comentari}}</h6>
                @endif
            </div>
        </li>
    @endforeach
</ul>



