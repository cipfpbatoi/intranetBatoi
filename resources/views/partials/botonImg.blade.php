<a href="{{$href}}" class="{{$class}}" id="{{$id}}"
   @isset($target) target="{{$target}}" @endisset
   @isset($rel) rel="{{$rel}}" @endisset
   @isset($ariaLabel) aria-label="{{$ariaLabel}}" @endisset
   @isset($title) title="{{$title}}" @endisset
   {!! $disabled !!}>
    @isset($img)
        <em class='fa {{$img}}' alt="{{$text}}" title="{{$text}}"></em>
    @else
        <em>{{$text}}</em>
    @endisset
    @isset($badge)
        <span class="badge">{{$badge}}</span>
    @endisset
</a>
