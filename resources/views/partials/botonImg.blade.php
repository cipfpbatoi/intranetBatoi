<a href="{{$href}}" class="{{$class}}" id="{{$id}}">
    @isset($img)
        <em class='fa {{$img}}' alt="{{$text}}" title="{{$text}}"></em>
    @else
        <em>{{$text}}</em>
    @endisset
</a>

