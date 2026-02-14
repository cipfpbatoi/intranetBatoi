<div class="" role="tabpanel" data-example-id="togglable-tabs">
    <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
        @foreach ($tabs as $index => $tab)
            <li role="presentation" class="{{ $loop->first ? 'active' : '' }}">
                <a href="#tab_content{{ $index }}" role="tab" data-toggle="tab"
                   aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                    @lang($tab['title'])
                </a>
            </li>
        @endforeach
    </ul>
    <div id="myTabContent" class="tab-content">
        @foreach ($tabs as $index => $tab)
            <div role="tabpanel" class="tab-pane fade {{ $loop->first ? 'active in' : '' }}"
                 id="tab_content{{ $index }}">
                @include($tab['view'], $tab['data'] ?? [])
            </div>
        @endforeach
    </div>
</div>
