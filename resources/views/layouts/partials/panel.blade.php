<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <x-layouts.titlebar>
                        {{ $panel->getTitulo() }}
                    </x-layouts.titlebar>
                    <div class="x_content">
                        {!! \Intranet\Services\UI\AppAlert::render() !!}
                    </div>
                    <div class="x_content">
                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                            <ul id="myTab1" class="nav nav-tabs bar_tabs right" role="tablist">
                                @foreach ($panel->getPestanas() as $pestana)
                                    <li role="presentation" class="{{$pestana->getActiva()}}">
                                        <a href="#tab_{{$pestana->getNombre()}}" id="{{$pestana->getNombre()}}-tabb" role="tab" data-toggle="tab" aria-controls="{{$pestana->getNombre()}}" aria-expanded="true">
                                            @if (strpos(trans("messages.buttons.".$pestana->getNombre()),'essages')==1)
                                                {{$pestana->getNombre()}}
                                            @else
                                                {{trans("messages.buttons.".$pestana->getNombre())}}
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div id="myTabContent1" class="tab-content">
                                @foreach ($panel->getPestanas() as $pestana)
                                    <div role="tabpanel" class="tab-pane fade {{$pestana->getActiva()}} in" id="tab_{{$pestana->getNombre()}}" aria-labelledby="{{$pestana->getNombre()}}-tab">
                                        @yield($pestana->getNombre())
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
