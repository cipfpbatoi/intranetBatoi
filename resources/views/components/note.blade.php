<article class="documento-ticket" data-color="{{ $color }}">
    <div class="documento-ticket-content">
        <span class="documento-ticket-type">{{ $name }}</span>
        <a class="documento-ticket-description" href="{{ $linkShow }}">{{ $message }}</a>
        @if ($title)
            <span class="documento-ticket-course">{{ $title }}</span>
        @endif
    </div>
    <div class="documento-ticket-actions">
        <a href="{{ $linkShow }}" title="Obrir document" aria-label="Obrir document">
            <i class="fa fa-eye" aria-hidden="true"></i>
        </a>
        @if ($linkEdit !== '#')
            <a href="{{ $linkEdit }}" title="Editar document" aria-label="Editar document">
                <i class="fa fa-edit" aria-hidden="true"></i>
            </a>
        @endif
    </div>
</article>
