<a href="{{$href}}" class="{{$class}}" id="{{$id}}">
    @isset($icon)
        <em class='fa {{$icon}}'></em>
    @endisset
    {{$text}}
</a>

