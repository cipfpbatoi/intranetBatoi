@php
    // Acceptem tant $alumno com $alumnoFct per compatibilitat amb diferents controladors
    $alumnoFct = $alumnoFct ?? null;
    $student = $alumno ?? optional($alumnoFct)->Alumno;
    $studentId = optional($student)->id;
    $alumnoFctId = optional($alumnoFct)->id;
    $title = $student ? 'Calendari FCT de ' . $student->fullName : 'Calendari FCT';
@endphp

<x-pages.livewire
    :title="$title"
    component="fct-calendar"
    :params="['alumno' => $studentId, 'alumnoFct' => $alumnoFctId]"
/>
