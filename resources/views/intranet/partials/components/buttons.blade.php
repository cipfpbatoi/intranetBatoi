@foreach ($panel->getBotones($tipo) as $button)
    @if (!empty($button))
        @if (isset($elemento))
            {{ $button->show($elemento) }}
        @else
            {{ $button->show() }}
        @endif
    @endif
@endforeach
