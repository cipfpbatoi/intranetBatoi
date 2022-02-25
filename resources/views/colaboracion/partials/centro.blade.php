<ul class="messages colaboracion">
    <div class="message_wrapper">
        <h5>
            <em class="fa fa-calendar user-profile-icon"></em> {!! $elemento->Centro->horarios !!}
        </h5>
    </div>
    @foreach ($elemento->Centro->Instructores as $instructor)
        <li>
            <div class="message_wrapper">
                <h5>
                    <em class="fa fa-user user-profile-icon"></em>{!! $instructor->id !!} - {{$instructor->nombre}}
                    <em class="fa fa-envelope user-profile-icon"></> {{$instructor->email}}
                </h5>
            </div>
        </li>
    @endforeach
</ul>