@php
    // Acceptem tant $alumno com $alumnoFct per compatibilitat amb diferents controladors
    $student = $alumno ?? optional($alumnoFct)->Alumno;
    $title = $student ? 'Calendari FCT de ' . $student->fullName : 'Calendari FCT';
@endphp

<x-pages.livewire
    :title="$title"
    component="fct-calendar"
    :params="['alumnoFct' => $student]"
/>
