<x-layouts.app :panel="$panel">
    @push('scripts')
        @include('intranet.partials.modal.index')
        @include('intranet.partials.modal.show')
        @include('intranet.partials.components.loadModals')
        @include('js.modaljs')
    @endpush
</x-layouts.app>