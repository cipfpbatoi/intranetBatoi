<div class="x_content">
    @if($panel->getPaginator())
        <div class="d-flex justify-content-end mb-2">
            <form method="get" class="w-100" style="max-width: 480px;">
                <div class="input-group input-group-sm">
                    <input type="text"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Cercar document...">
                    <button class="btn btn-primary" type="submit">{{ __('Cercar') }}</button>
                    @if(request('search'))
                        <a class="btn btn-outline-secondary" href="{{ url()->current() }}">{{ __('Netejar') }}</a>
                    @endif
                </div>
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
</div>
