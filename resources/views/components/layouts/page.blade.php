<div class="right_col" role="main">
    <div class="">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                    {{-- Títol --}}

                    <x-layouts.titlebar>
                        @yield('titulo')
                    </x-layouts.titlebar>


                    {{-- Contingut --}}
                    <div class="x_content">
                        @yield('content')
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