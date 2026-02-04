@props([
    'href' => '#',
    'id' => null,
    'buttonClass' => '',
    'target' => null,
    'rel' => null,
    'ariaLabel' => null,
    'title' => null,
    'disabledAttr' => '',
    'icon' => null,
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
    @isset($icon)
        <em class='fa {{$icon}}'></em>
    @endisset
    {{$text}}
    @isset($badge)
        <span class="badge">{{$badge}}</span>
    @endisset
</a>
