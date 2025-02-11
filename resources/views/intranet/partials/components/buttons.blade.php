@foreach ($panel->getBotones($tipo) as $index => $button)
    @if (!empty($button))
        @isset($elemento)
            {!! $button->show($elemento) !!}
        @else
            {!! $button->show() !!}
        @endisset
    @endif
@endforeach
