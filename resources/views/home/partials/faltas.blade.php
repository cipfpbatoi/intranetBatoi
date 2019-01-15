<ul class="messages">
    <li>
        @foreach ($faltas as $falta)
            @if ($falta->profesor->activo)
            <img src="/img/ill.png" class="avatar" alt="Avatar">
            <div class="message_date">
                HUI
            </div>
            <div class="message_wrapper">
                <h4 class="heading">{{$falta->profesor->fullName}}</h4>
                <br />
            </div>
            @endif
        @endforeach
    </li>
</ul>