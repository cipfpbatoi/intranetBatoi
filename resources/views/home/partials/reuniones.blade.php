<br/>
<ul class="messages">
    @foreach ($reuniones as $reunion)
    <li>
        <img src="/img/actas.png" class="avatar" alt="Avatar">
        <div class="message_date">
           <h3 class="date text-info">{{day($reunion->fecha)}}</h3>
            <p class="month">{{month($reunion->fecha)}}</p>
            <p class="hour">{{hour($reunion->fecha)}}</p>
        </div>
        <div class="message_wrapper" style="float:right">
            @foreach ($reunion->Profesores as $profesor)
                <p style="font-size: xx-small">{{$profesor->shortName}}</p>
            @endforeach
        </div>
        <div class="message_wrapper">
            <h4 class="heading">{{$reunion->xgrupo}}  </h4>
            <blockquote class="message"></blockquote>
            <br />
            <p class="url">
                <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                {{$reunion->descripcion}}
            </p>
            <p class="url">
                <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                {{$reunion->Espacio->descripcion}}
            </p>
        </div>

    </li>
    @endforeach
</ul>