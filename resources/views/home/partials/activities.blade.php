<ul class="messages">
    @foreach ($actividades as $actividad)
        <x-llist image="actividad.png" date="{{$actividad->desde}}">
            @isset($actividad->Tutor)
                <h4 class="heading">
                    {{$actividad->Tutor->first()->shortName}}
                </h4>
            @endisset
            <blockquote class="message">{{$actividad->name}}</blockquote>
            <br/>
            <p class="url">
                <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                @foreach ($actividad->grupos as $grupo)
                    <em class="fa fa-group"></em>
                    {{ $grupo->nombre }}
                @endforeach
            </p>
        </x-llist>
    @endforeach
</ul>
