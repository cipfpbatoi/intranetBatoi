    @foreach ($items as $item)
        <li id="menu_{{ $item['id']}}">
        <a href="{{ $item['url'] }}">
            <i @if ($item['class']) class="fa {{ $item['class'] }}" @endif  ></i>
            {{ trans('messages.menu.'.$item['title']) }}
            @if (!empty($item['submenu']))
            <span class="fa fa-chevron-down"></span>       
            @endif
        </a>

            @if (!empty($item['submenu']))
                <ul class="nav child_menu">
                    @foreach ($item['submenu'] as $subitem)
                        <li>
                             @if (isset($subitem['full-url']))
                                <a href="{{ $subitem['full-url'] }}" target="_blank">{{trans('messages.menu.'. $subitem['title']) }}</a>
                            @else
                                <a href="{{ $subitem['url'] }}">{{trans('messages.menu.'. $subitem['title']) }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach