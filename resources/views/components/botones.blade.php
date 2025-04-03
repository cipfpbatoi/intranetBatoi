@if($centrado)
    <div class="centrado">
        @foreach ($botones as $html)
            {!! $html !!}
        @endforeach
    </div>
@else
    @foreach ($botones as $html)
        {!! $html !!}
    @endforeach
@endif