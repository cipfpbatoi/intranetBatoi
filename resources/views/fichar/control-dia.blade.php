<x-layouts.app title="Control diari fitxages">

    @push('styles')
        @livewireStyles
    @endpush

    @livewire(\Intranet\Livewire\FicharControlDia::class)

    @push('scripts')
        @livewireScriptConfig
        @livewireScripts
        <script>
            if (window.Livewire && typeof Livewire.all === 'function' && Livewire.all().length === 0 && typeof Livewire.start === 'function') {
                Livewire.start();
            }
        </script>
    @endpush

</x-layouts.app>
