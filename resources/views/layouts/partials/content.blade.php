<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        @include('layouts.partials.titlecontent')
                    </div>
                    <div class="x_content">
                        {!! Alert::render() !!}
                    </div>
                    <div class="x_content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->
