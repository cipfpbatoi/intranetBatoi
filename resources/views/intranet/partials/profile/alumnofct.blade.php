@php
    $misElementos = hazArray($panel->getElementos($pestana),'idFct','idFct');
@endphp
@php
    $fcts = \Intranet\Entities\Fct::with('Instructor')
    ->whereIn('id',$misElementos)
    ->get();
@endphp
@foreach ($fcts as $fct)
    @php
        $contactos = \Intranet\Entities\Activity::mail()
        ->Modelo('Fct')
        ->id($fct->id)
        ->orderBy('created_at')
        ->get();
        $alumnos = $fct->Alumnos;
    @endphp

    @include('intranet.partials.profile.partials.fct')
@endforeach

