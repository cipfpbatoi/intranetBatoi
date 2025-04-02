@props(['id', 'pestanyes'])

<div class="x_content">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="{{ $id }}" class="nav nav-tabs bar_tabs right" role="tablist">
            @foreach ($pestanyes as $pestana)
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
            @foreach ($pestanyes as $pestana)
                <div role="tabpanel"
                     class="tab-pane fade {{ $pestana->getActiva() }} in"
                     id="tab_{{ $pestana->getNombre() }}"
                     aria-labelledby="{{ $pestana->getNombre() }}-tab">
                    @yield($pestana->getNombre())
                </div>
            @endforeach
        </div>
    </div>
</div>