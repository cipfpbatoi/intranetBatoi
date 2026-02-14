<li>
    <img src="img/{{$image}}" class="avatar" alt="avatar" />
    <div class="message_date" title="venciment">
        @if (!is_numeric(strtotime($date)))
            <h4 class="date text-info">{{ $date }}</h4>
        @else
            <h4 class="date text-info">{{ day($date) }} {{ month($date) }}</h4>
            @if (hour($date) != "00:00" )
                <h4 class="date text-info">{{ hour($date) }}</h4>
            @endif
        @endif
    </div>
    <div class="message_wrapper">
        <h4 class="heading">
            {{ $slot }}
        </h4>
    </div>
</li>

