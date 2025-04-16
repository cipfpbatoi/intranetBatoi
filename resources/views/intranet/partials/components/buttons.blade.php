@foreach ($panel->getBotones($tipo) as $button)
    @if (!empty($button))
        {!! $button->show($elemento ?? null) !!}
    @endif
@endforeach