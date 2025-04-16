<div class="x_title">
    <h2>{{ $slot }}</h2>

    <ul class="nav navbar-right panel_toolbox">
        {{-- Botó enrere --}}
        <li>
            <a href="{{ $href }}" >
                <i class="fa fa-reply"></i>
            </a>
        </li>

        {{-- Idiomes --}}
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ url('lang', ['ca']) }}">Valencià</a></li>
                <li><a href="{{ url('lang', ['es']) }}">Castellano</a></li>
                <li><a href="{{ url('lang', ['en']) }}">English</a></li>
            </ul>
        </li>

        {{-- Ajuda contextual --}}
        @if ($ajuda)
            <li>
                <a id="question" href="https://cipfpbatoi.github.io/intranetBatoi/{{ $ajuda }}" target="_blank">
                    <i class="fa fa-question"></i>
                </a>
            </li>
        @endif
    </ul>

    <div class="clearfix"></div>
</div>
