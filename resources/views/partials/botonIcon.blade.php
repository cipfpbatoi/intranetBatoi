<a href="{{$href}}" class="{{$class}}" id="{{$id}}"{!! $disabled !!}>
    @isset($icon)
        <em class='fa {{$icon}}'></em>
    @endisset
    {{$text}}
</a>
