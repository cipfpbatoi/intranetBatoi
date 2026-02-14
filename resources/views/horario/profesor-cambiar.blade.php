<x-pages.livewire
    title="Horari {{ $profesor->fullName }}"
    component="horari-professor-canvi"
    :params="['dni' => $profesor->dni]"
/>
