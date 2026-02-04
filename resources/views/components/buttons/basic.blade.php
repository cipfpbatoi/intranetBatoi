@props([
    'href' => '#',
    'id' => null,
    'buttonClass' => '',
    'onclick' => null,
    'target' => null,
    'rel' => null,
    'ariaLabel' => null,
    'title' => null,
    'disabledAttr' => '',
    'dataAttrs' => '',
    'icon' => null,
    'text' => '',
    'badge' => null,
])

<a href="{{$href}}"
   @isset($id) id="{{$id}}" @endisset
   class="{{$buttonClass}}"
   @isset($onclick) onclick="{{$onclick}}" @endisset
   @isset($target) target="{{$target}}" @endisset
   @isset($rel) rel="{{$rel}}" @endisset
   @isset($ariaLabel) aria-label="{{$ariaLabel}}" @endisset
   @isset($title) title="{{$title}}" @endisset
   {!! $disabledAttr !!}
   {!! $dataAttrs !!}
>
    @isset($icon)
        <em class='{{$icon}}'></em>
    @endisset
    {{$text}}
    @isset($badge)
        <span class="badge">{{$badge}}</span>
    @endisset
</a>
