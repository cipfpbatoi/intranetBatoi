<input class="{{$class}}" id="{{$id}}"
       @isset($ariaLabel) aria-label="{{$ariaLabel}}" @endisset
       @isset($title) title="{{$title}}" @endisset
       {!! $disabled !!} {!! $data !!} type='submit'  value="{{$text}}" />
