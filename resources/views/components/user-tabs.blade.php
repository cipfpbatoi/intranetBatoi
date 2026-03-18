<div class="" role="tabpanel" data-example-id="togglable-tabs">
    <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
        @foreach ($tabs as $index => $tab)
            <li class="nav-item" role="presentation">
                <a href="#tab_content{{ $index }}" class="nav-link {{ $loop->first ? 'active' : '' }}" role="tab" data-bs-toggle="tab"
                   aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    @lang($tab['title'])
                </a>
            </li>
        @endforeach
    </ul>
    <div id="myTabContent" class="tab-content">
        @foreach ($tabs as $index => $tab)
            <div role="tabpanel" class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                 id="tab_content{{ $index }}">
                @include($tab['view'], $tab['data'] ?? [])
            </div>
        @endforeach
    </div>
</div>
