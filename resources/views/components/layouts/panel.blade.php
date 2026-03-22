<div class="right_col" role="main">
    <div class="">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                     <div class="x_content">
                         {!! \Intranet\Services\UI\AppAlert::render() !!}
                     </div> 
                    {{-- Títol del panell --}}
                    <x-layouts.titlebar>
                        {{  $title }}
                    </x-layouts.titlebar>
                    {{-- Pestanyes --}}

                     <x-ui.tabs id="myTab1" :panel="$panel" />
                    
                </div>
            </div>
        </div>
    </div>
</div>
