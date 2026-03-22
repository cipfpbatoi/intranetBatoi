@php
    $colaboracion = $fct->relatedColaboracion();
    $centro = $fct->relatedCenter();
@endphp

<ul class="messages colaboracion">
    <div class="message_wrapper">
        <h5>
            <em class="fa fa-calendar user-profile-icon"></em>
            Horari: {!! $centro ? $centro->horarios : '-' !!}
            <br/><br/>
            @if ($centro && $centro->observaciones)
                <em class="fa fa-eye user-profile-icon"></em> {!! $centro->observaciones !!}<br/><br/>
            @endif
            @if ($colaboracion && $colaboracion->votes && count($colaboracion->votes))
                <a href="{{ route('votes.colaboracion', ['colaboracion' => $colaboracion->id]) }}">
                    <i class="fa fa-bar-chart"></i> Poll
                </a>
                <br/><br/>
            @endif
            <em class="fa fa-group user-profile-icon"></em> Instructors:
            <ul>
                @if ($centro && $centro->Instructores)
                    @foreach ($centro->Instructores as $instructor)
                        <li>
                            <em class="fa fa-user user-profile-icon"></em>
                            {!! $instructor->id !!} - {{ $instructor->nombre }}
                            <em class="fa fa-envelope user-profile-icon"></em>
                            {{ $instructor->email }}
                        </li>
                    @endforeach
                @else
                    <li>@lang("messages.generic.empty")</li>
                @endif
            </ul>
        </h5>
    </div>
    @if ($centro)
        <a href="{{ route('instructor.create', ['centro' => $centro->id]) }}">
            <em class="fa fa-plus"></em> @lang("messages.generic.anadir") @lang("models.modelos.Instructor")
        </a>
    @endif
</ul>
