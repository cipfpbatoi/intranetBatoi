<div class="right_col" role="main">
    <div class="">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                    {{-- Títol del panell --}}
                    <x-layouts.titlebar>
                        @yield('titulo')
                    </x-layouts.titlebar>

                    {{-- Pestanyes --}}
                    <div class="x_content">
                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                            <ul id="myTab1" class="nav nav-tabs bar_tabs right" role="tablist">
                                @foreach ($panel->getPestanas() as $pestana)
                                    <li role="presentation" class="{{ $pestana->getActiva() }}">
                                        <a href="#tab_{{ $pestana->getNombre() }}"
                                           id="{{ $pestana->getNombre() }}-tabb"
                                           role="tab"
                                           data-toggle="tab"
                                           aria-controls="{{ $pestana->getNombre() }}"
                                           aria-expanded="true">
                                            @php
                                                $label = trans("messages.buttons." . $pestana->getNombre());
                                            @endphp
                                            {{ Illuminate\Support\Str::contains($label, 'essages') ? $pestana->getNombre() : $label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div id="myTabContent1" class="tab-content">
                                @foreach ($panel->getPestanas() as $pestana)
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

                    {{-- Alertes --}}
                    <div class="x_content">
                        {!! Alert::render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
