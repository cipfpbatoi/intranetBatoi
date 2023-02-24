<a href="{{$href}}" @isset($id) id="{{$id}}" @endisset class="{{$class}}" {!! $data !!}>
    @isset($icon)
        <em class='{{$icon}}'></em>
    @endisset
    {{$text}}
</a>

