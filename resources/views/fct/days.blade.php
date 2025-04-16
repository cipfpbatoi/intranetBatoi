@php($title = 'Calendari FCT de '.$alumnoFct->fullName)
<x-pages.livewire
        :title="$title"
         component="fct-calendar"
        :params="['alumnoFct' => $alumnoFct]"
/>