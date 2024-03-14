<a href="{{$href}}" class="{{$class}} js-temp-disabled" id="{{$id}}" disabled>
    @isset($icon)
        <em class='fa {{$icon}}'></em>
    @endisset
    {{$text}}
</a>

