<ul class="messages colaboracion">
    @foreach ($contactAl as $contacto)
        <li>
            <div class="message_wrapper">
                <h5>
                    <i class="fa fa-calendar user-profile-icon"></i> {!! $contacto->created_at !!}
                    <i class="fa fa-mortar-board user-profile-icon"></i> {{ \Intranet\Entities\Alumno::find($contacto->model_id)->fullName }}
                    <i class="fa fa-envelope user-profile-icon"></i> {{$contacto->comentari}}
                    <i class="fa fa-user user-profile-icon"></i> {{$contacto->Propietario->fullName}}
                </h5>
            </div>
        </li>
    @endforeach
</ul>