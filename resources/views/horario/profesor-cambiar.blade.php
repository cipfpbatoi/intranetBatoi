<x-pages.livewire
    title="Horari {{ $profesor->fullName ?? $dni }}"
    component="horari-professor-canvi"
    :params="['dni' => $dni]"
/>
