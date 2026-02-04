@props([
    'href' => '#',
    'id' => null,
    'buttonClass' => '',
    'target' => null,
    'rel' => null,
    'ariaLabel' => null,
    'title' => null,
    'disabledAttr' => '',
    'img' => null,
    'text' => '',
    'badge' => null,
])

<a href="{{$href}}" class="{{$buttonClass}}"
   @isset($id) id="{{$id}}" @endisset
   @isset($target) target="{{$target}}" @endisset
   @isset($rel) rel="{{$rel}}" @endisset
   @isset($ariaLabel) aria-label="{{$ariaLabel}}" @endisset
   @isset($title) title="{{$title}}" @endisset
   {!! $disabledAttr !!}>
    @isset($img)
        <em class='fa {{$img}}' alt="{{$text}}" title="{{$text}}"></em>
    @else
        <em>{{$text}}</em>
    @endisset
    @isset($badge)
        <span class="badge">{{$badge}}</span>
    @endisset
</a>
