@props([
    'panel',
    'pestana' => null,
    'elementos' => [],
    'mostrarBody' => true,
    'id' => 'datatable'
])

<div class="x_content">
    <table id="{{ $id }}"
           name="{{ $panel->getModel() }}"
           class="table table-striped table-bordered display nowrap dtr-inline collapsed"
           style="width:100%" data-page-length="25"
           @if ($panel->getPaginator()) data-server-pagination="true" @endif>

        <thead>
            <x-grid.header :panel="$panel" />
        </thead>

        @if($mostrarBody)
            <tbody>
            @forelse($elementos as $elemento)
                <x-grid.row :elemento="$elemento" :panel="$panel" :pestana="$pestana" />
            @empty
                @php($cols = count($panel->getRejilla()) + 1)
                <tr>
                    @for ($i = 0; $i < $cols; $i++)
                        @if ($i === 0)
                            <td class="text-center">@lang('No hi ha dades disponibles')</td>
                        @else
                            <td></td>
                        @endif
                    @endfor
                </tr>
            @endforelse
            </tbody>
        @endif

        <tfoot>
            <x-grid.header :panel="$panel" />
        </tfoot>
    </table>
    @if ($panel->getPaginator())
        <div class="text-center">
            {{ $panel->getPaginator()->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
