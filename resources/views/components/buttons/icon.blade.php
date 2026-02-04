<a href="{{$href}}" class="{{$class}}"
   @isset($id) id="{{$id}}" @endisset
   @isset($target) target="{{$target}}" @endisset
   @isset($rel) rel="{{$rel}}" @endisset
   @isset($ariaLabel) aria-label="{{$ariaLabel}}" @endisset
   @isset($title) title="{{$title}}" @endisset
   {!! $disabled !!}>
    @isset($icon)
        <em class='fa {{$icon}}'></em>
    @endisset
    {{$text}}
    @isset($badge)
        <span class="badge">{{$badge}}</span>
    @endisset
</a>
