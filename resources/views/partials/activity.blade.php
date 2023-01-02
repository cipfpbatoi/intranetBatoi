@isset($class)
    {{$fecha}}  <em class="fa fa-{{$class}}"></em>
@else
    <a href="#" class="small dragable" id="{{$id}}">
    @isset($comentari)
            <em class="fa fa-plus"></em>
    @else
            <em class="fa fa-minus"></em>
    @endisset
    {{$fecha}} <em class="fa fa-{{$action}}"></em>
    </a>
@endif
