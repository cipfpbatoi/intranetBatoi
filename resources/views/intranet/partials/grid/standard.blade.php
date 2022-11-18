<div class="x_content">
    <table id='datatable'
           name='{{$panel->getModel()}}'
           class="table table-striped table-bordered display nowrap dtr-inline collapsed"
           style='width:100%' data-page-length="25">
        <thead>
            @include('intranet.partials.grid.campos')
        </thead>
        <tbody>
            @include('intranet.partials.grid.lineas')
        </tbody>
        <tfoot>
            @include('intranet.partials.grid.campos')
        </tfoot>
    </table>
</div>
