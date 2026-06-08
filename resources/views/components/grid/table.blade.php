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
            <x-grid.header :panel="$panel" :pestana="$pestana" />
        </thead>

        @if($mostrarBody)
            <tbody>
            @foreach($elementos as $elemento)
                <x-grid.row :elemento="$elemento" :panel="$panel" :pestana="$pestana" />
            @endforeach
            </tbody>
        @endif

        <tfoot>
            <x-grid.header :panel="$panel" :pestana="$pestana" />
        </tfoot>
    </table>
</div>
