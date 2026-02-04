@props([
    'id' => null,
    'buttonClass' => '',
    'ariaLabel' => null,
    'title' => null,
    'disabledAttr' => '',
    'dataAttrs' => '',
    'text' => '',
])

<input class="{{$buttonClass}}" id="{{$id}}"
       @isset($ariaLabel) aria-label="{{$ariaLabel}}" @endisset
       @isset($title) title="{{$title}}" @endisset
       {!! $disabledAttr !!} {!! $dataAttrs !!} type='submit'  value="{{$text}}" />
