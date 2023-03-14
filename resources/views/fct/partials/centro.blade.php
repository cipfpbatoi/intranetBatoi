<ul class="messages colaboracion">
    <div class="message_wrapper">
        <h5>
            <em class="fa fa-calendar user-profile-icon"></em>
            Horari:  {!! $fct->Colaboracion->Centro->horarios !!}
            <br/><br/>
            @isset($fct->Colaboracion->Centro->observaciones)
                <em class="fa fa-eye user-profile-icon"></em> {!! $fct->Colaboracion->Centro->observaciones!!}<br/><br/>
            @endisset
            @if (count($fct->Colaboracion->votes))
                <a href="/votes/{{$fct->Colaboracion->id}}/show"> <i class="fa fa-bar-chart"></i> Poll</a><br/><br/>
            @endif
            <em class="fa fa-group user-profile-icon"></em> Instructors:
            <ul>
                @foreach ($fct->Colaboracion->Centro->Instructores as $instructor)
                    <li>
                        <em class="fa fa-user user-profile-icon"></em>
                        {!! $instructor->id !!} - {{$instructor->nombre}}
                        <em class="fa fa-envelope user-profile-icon"></em>
                        {{$instructor->email}}
                    </li>
                @endforeach
            </ul>
        </h5>
    </div>
    <a href="/instructor/{{$fct->Colaboracion->Centro->id}}/create">
        <em class="fa fa-plus"></em> @lang("messages.generic.anadir") @lang("models.modelos.Instructor")
    </a>
</ul>
