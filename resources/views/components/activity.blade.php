 <small>
    @if($class)
        {{ $fecha }} <em class="fa fa-{{ $class }}"></em>
    @else
        <a href="#" class="small dragable" id="{{ $activity->id }}">
            @if($activity->comentari)
                <em class="fa fa-plus"></em>
            @else
                <em class="fa fa-minus"></em>
            @endif
            {{ $fecha }} <em class="fa fa-{{ $action }}"></em>
        </a>
    @endif
</small>