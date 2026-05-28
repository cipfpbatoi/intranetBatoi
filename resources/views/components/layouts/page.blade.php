<div class="right_col" role="main">
    <div class="">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                    {{-- Títol --}}

                    <x-layouts.titlebar>
                        {{  $title }}
                    </x-layouts.titlebar>

                    {{-- Alertes --}}
                    <div class="x_content">
                        {!! \Intranet\Services\UI\AppAlert::render() !!}
                        <x-ui.errors />
                    </div>

                    {{-- Contingut --}}
                    <div class="x_content">
                        {{ $slot }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
