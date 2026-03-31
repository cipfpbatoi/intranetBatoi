<x-layouts.app :title="'Detall col·laboració ' . ($elemento->Empresa ?? $elemento->id)">
    <div class="mb-3">
        <a href="{{ $returnUrl ?? url()->previous() }}" class="btn btn-default btn-sm">
            <em class="fa fa-arrow-left"></em> Tornar
        </a>
    </div>

    @include('colaboracion.partials.show')
</x-layouts.app>
