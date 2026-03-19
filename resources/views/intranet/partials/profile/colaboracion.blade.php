@foreach ($panel->getElementos($pestana) as $elemento)
    @php
        $contactos = $elemento->contactos ?? collect();
        $isMine = $elemento->tutor === authUser()->dni;
    @endphp
    @include ('intranet.partials.profile.partials.colaboracion')
@endforeach
