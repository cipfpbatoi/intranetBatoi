<a href="{{$href}}"
   @isset($id) id="{{$id}}" @endisset
   class="{{$class}}"
   @isset($onclick) onclick="{{$onclick}}" @endisset
   @isset($target) target="{{$target}}" @endisset
   @isset($rel) rel="{{$rel}}" @endisset
   @isset($ariaLabel) aria-label="{{$ariaLabel}}" @endisset
   @isset($title) title="{{$title}}" @endisset
   {!! $disabled !!}
   {!! $data !!}
>
    @isset($icon)
        <em class='{{$icon}}'></em>
    @endisset
    {{$text}}
    @isset($badge)
        <span class="badge">{{$badge}}</span>
    @endisset
</a>
