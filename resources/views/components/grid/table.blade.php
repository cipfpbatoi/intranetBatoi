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
           style="width:100%" data-page-length="25">

        <thead>
            <x-grid.header :panel="$panel" />
        </thead>

        @if($mostrarBody)
            <tbody>
            @forelse($elementos as $elemento)
                <x-grid.row :elemento="$elemento" :panel="$panel" :pestana="$pestana" />
            @empty
                <tr>
                    <td colspan="{{ count($panel->getRejilla()) + 1 }}" class="text-center">
                        @lang('No hi ha dades disponibles')
                    </td>
                </tr>
            @endforelse
            </tbody>
        @endif

        <tfoot>
            <x-grid.header :panel="$panel" />
        </tfoot>
    </table>
</div>