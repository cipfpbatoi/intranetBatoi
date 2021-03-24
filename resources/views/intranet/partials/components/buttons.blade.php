@foreach ($panel->getBotones($tipo) as $button)
    @if (isset($elemento) && $button!='')
        {{ $button->show($elemento) }}
    @else
        {{ $button->show() }}
    @endif    
@endforeach
