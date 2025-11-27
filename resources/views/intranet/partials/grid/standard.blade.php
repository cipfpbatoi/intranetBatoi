<div class="x_content">
    @if($panel->getPaginator())
        <div class="d-flex justify-content-end mb-2">
            <form method="get" class="d-flex align-items-center gap-2 flex-nowrap" style="max-width: 520px;">
                <label for="serverSearchInput" class="mb-0">{{ __('Filtrar') }}:</label>
                <input type="text"
                       name="search"
                       id="serverSearchInput"
                       class="form-control form-control-sm"
                       style="min-width: 220px;"
                       value="{{ request('search') }}"
                       placeholder="Cercar document...">
            </form>
        </div>
    @endif
    <table id='datatable'
           name='{{$panel->getModel()}}'
           class="table table-striped table-bordered display nowrap dtr-inline collapsed"
           style='width:100%' data-page-length="25"
           data-server-pagination="{{ $panel->getPaginator() ? 'true' : 'false' }}">
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
    @if($panel->getPaginator())
        <div class="mt-3 d-flex justify-content-center">
            {{ $panel->getPaginator()->links('pagination::bootstrap-4') }}
        </div>
    @endif
    @if($panel->getPaginator())
        <script>
            (function() {
                const input = document.getElementById('serverSearchInput');
                if (!input) return;
                let timer;
                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        input.form.submit();
                    }, 400);
                });
            })();
        </script>
    @endif
</div>
