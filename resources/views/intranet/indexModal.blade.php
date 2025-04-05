 <x-layouts.app>

     @php($pestana = $panel->getPestanas()[0])

    {{-- Slot per a scripts (substitueix @section('scripts')) --}}
    <x-slot name="scripts">
        @include('intranet.partials.modal.index')
        @include('intranet.partials.components.loadModals')
        @include('js.modaljs')
    </x-slot>

</x-layouts.app>