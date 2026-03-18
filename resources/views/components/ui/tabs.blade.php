@props(['id', 'pestanyes','panel'])

<div class="x_content">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="{{ $id }}" class="nav nav-tabs bar_tabs right" role="tablist">
            @foreach ($panel->getPestanas() as $pestana)
                @php($isActive = trim((string) $pestana->getActiva()) !== '')
                <li class="nav-item" role="presentation">
                    <a href="#tab_{{ $pestana->getNombre()  }}"
                       class="nav-link {{ $isActive ? 'active' : '' }}"
                       id="{{ $pestana->getNombre() }}-tab"
                       role="tab"
                       data-bs-toggle="tab"
                       aria-controls="{{ $pestana->getNombre() }}"
                       aria-selected="{{ $isActive ? 'true' : 'false' }}">
                        {{ $pestana->getLabel() }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div id="{{ $id }}Content" class="tab-content">
            @foreach ($panel->getPestanas() as $pestana)
                @php($isActive = trim((string) $pestana->getActiva()) !== '')
                <div role="tabpanel"
                     class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                     id="tab_{{ $pestana->getNombre() }}"
                     aria-labelledby="{{ $pestana->getNombre() }}-tab">
                        <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" /><br/>
                        @include($pestana->getVista(),$pestana->getFiltro())
                </div>
            @endforeach
        </div>
    </div>
</div>
