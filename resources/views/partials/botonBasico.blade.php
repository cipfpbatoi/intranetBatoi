<a href="{{$href}}" class="{{$class}}" {!! $data !!}>
    @isset($icon)
        <em class='{{$icon}}'></em>
    @endisset
    {{$text}}
</a>

