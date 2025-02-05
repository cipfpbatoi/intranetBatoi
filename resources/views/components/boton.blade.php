<a href="{{ $href }}" class="{{ $class }}" id="{{ $id }}"
    @if($onclick) onclick="{{ $onclick }}" @endif>
    @if($img)
        <img src="{{ asset($img) }}" alt="Botó">
    @elseif($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $text }}
</a>
