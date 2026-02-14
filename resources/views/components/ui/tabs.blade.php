@props(['id', 'pestanyes','panel'])

<div class="x_content">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="{{ $id }}" class="nav nav-tabs bar_tabs right" role="tablist">
            @foreach ($panel->getPestanas() as $pestana)
                <li role="presentation" class="{{ $pestana->getActiva() }}">
                    <a href="#tab_{{ $pestana->getNombre()  }}"
                       id="{{ $pestana->getNombre() }}-tab"
                       role="tab"
                       data-toggle="tab"
                       aria-controls="{{ $pestana->getNombre() }}"
                       aria-expanded="true">
                        {{ $pestana->getLabel() }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div id="{{ $id }}Content" class="tab-content">
            @foreach ($panel->getPestanas() as $pestana)
                <div role="tabpanel"
                     class="tab-pane fade {{ $pestana->getActiva() }} in"
                     id="tab_{{ $pestana->getNombre() }}"
                     aria-labelledby="{{ $pestana->getNombre() }}-tab">
                        <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" /><br/>
                        @include($pestana->getVista(),$pestana->getFiltro())
                </div>
            @endforeach
        </div>
    </div>
</div>