<div class="x_content">
    @if($panel->getPaginator())
        <div class="d-flex justify-content-end mb-2">
            <form method="get"
                  id="serverFilterForm"
                  class="d-flex align-items-center gap-2 flex-wrap justify-content-end"
                  style="max-width: 820px;">
                <label for="serverSearchInput" class="mb-0">{{ __('Filtrar') }}:</label>
                <input type="text"
                       name="search"
                       id="serverSearchInput"
                       class="form-control form-control-sm server-filter"
                       style="min-width: 220px;"
                       value="{{ request('search') }}"
                       placeholder="Cercar document...">

                @if(!empty($panel->filterTipoOptions))
                    <select name="tipoDocumento"
                            class="form-select form-select-sm server-filter"
                            style="width: 150px;"
                            id="serverFilterTipo">
                        <option value="">{{ __('Tipus') }}</option>
                        @foreach(($panel->filterTipoOptions ?? []) as $key => $label)
                            <option value="{{ $key }}" @selected(request('tipoDocumento') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                @endif

                @if(!empty($panel->filterCursoOptions))
                    <select name="curso"
                            class="form-select form-select-sm server-filter"
                            style="width: 120px;"
                            id="serverFilterCurso">
                        <option value="">{{ __('Curs') }}</option>
                        @foreach(($panel->filterCursoOptions ?? []) as $curso)
                            <option value="{{ $curso }}" @selected(request('curso') == $curso)>{{ $curso }}</option>
                        @endforeach
                    </select>
                @endif

                @if(!empty($panel->filterPropietario))
                    <input type="text"
                           name="propietario"
                           id="serverFilterPropietario"
                           class="form-control form-control-sm server-filter"
                           style="width: 160px;"
                           value="{{ request('propietario') }}"
                           placeholder="{{ __('Propietari') }}">
                @endif

                @if(!empty($panel->filterTags))
                    <input type="text"
                           name="tags"
                           id="serverFilterTags"
                           class="form-control form-control-sm server-filter"
                           style="width: 160px;"
                           value="{{ request('tags') }}"
                           placeholder="{{ __('Tags') }}">
                @endif
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
                const form = document.getElementById('serverFilterForm');
                if (!form) return;
                const inputs = form.querySelectorAll('.server-filter');
                let timer;
                const submitDebounced = () => {
                    clearTimeout(timer);
                    timer = setTimeout(() => form.submit(), 300);
                };
                inputs.forEach((el) => {
                    const evt = el.tagName === 'SELECT' ? 'change' : 'input';
                    el.addEventListener(evt, submitDebounced);
                });
            })();
        </script>
    @endif
</div>
