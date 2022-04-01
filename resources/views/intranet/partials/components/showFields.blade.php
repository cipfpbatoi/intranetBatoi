<span id="campos">
    <ul>
        @foreach ($fields as $key => $value)
            @if (is_array($value))
                @foreach ($value as $secondKey => $realValue)
                    <li>@lang('validation.attributes.'.$key) {{$secondKey}}: {{ $realValue }}</li>
                @endforeach
            @else
                <li>@lang('validation.attributes.'.$key) : {{ $value }}</li>
            @endif
        @endforeach
    </ul>
</span>