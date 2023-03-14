@php
    $misElementos = $panel->getElementos($pestana)->where('tutor',authUser()->dni);
    $otros =  $panel->getElementos($pestana)->where('tutor','!=',authUser()->dni);
@endphp
@foreach ($misElementos as $elemento)
        @php
            $contactos = \Intranet\Entities\Activity::modelo('Colaboracion')
            ->id($elemento->id)
            ->notUpdate()
            ->orderBy('created_at')
            ->get();
        @endphp
        @include ('intranet.partials.profile.partials.colaboracion')
@endforeach
@foreach ($otros as $elemento)
    @php
        $contactos = \Intranet\Entities\Activity::modelo('Colaboracion')
        ->id($elemento->id)
        ->orderBy('created_at')
        ->get();
    @endphp
    @include ('intranet.partials.profile.partials.colaboracion')
@endforeach
