@php
    $elementos = $panel->getElementos($pestana)->sortBy('localidad')->values();
    $localidadActual = null;
@endphp

@foreach ($elementos as $elemento)
    @php
        $contactos = $elemento->contactos ?? collect();
        $isMine = $elemento->tutor === authUser()->dni;
        $localidad = $elemento->localidad;
    @endphp
    @if ($localidadActual !== $localidad)
        @php($localidadActual = $localidad)
        <div class="col-xs-12" style="margin-top: 8px; margin-bottom: 6px;">
            <div style="padding: 6px 10px; border-left: 4px solid #1abb9c; background: #f7f9fb;">
                <strong><em class="fa fa-map-marker"></em> {{ $localidadActual }}</strong>
            </div>
        </div>
    @endif
    @include ('intranet.partials.profile.partials.colaboracion')
@endforeach
