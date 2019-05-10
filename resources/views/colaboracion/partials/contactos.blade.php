<ul class="messages colaboracion">
    @foreach ($contactCol as $contacto)
        <li>
            <div class="message_wrapper">
                <h4><i class="fa fa-calendar user-profile-icon"> </i> {!! $contacto->created_at !!}
                <h4><i class="fa fa-envelope user-profile-icon"></i>{{$contacto->comentaril}}</h4>
                <h4 class="text-info">{{$contacto->Propietario->fullName}}</h4>
            </div>
        </li>
    @endforeach
</ul>



