<a href="{{$href}}"
   @isset($id) id="{{$id}}" @endisset
   class="{{$class}}"
   @isset($onclick) onclick="{{$onclick}}" @endisset
   {!! $disabled !!}
        {!! $data !!}
>
    @isset($icon)
        <em class='{{$icon}}'></em>
    @endisset
    {{$text}}
</a>
