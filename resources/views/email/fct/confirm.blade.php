
<p>Hola sóc {{AuthUser()->fullName}}.</p>
<p>T'escric per tal de confirmar la visita al centre de treball per fer el seguiment de les practiques FCT dels alumnes del {{config('contacto.nombre')}}.</p>
<p>L'horari que em quedat és <strong>{{$elemento->pivot->hora_ini}} del {{FechaString($mail->getToPeople())}}</strong></p>

<p>Salutacions cordials de {{AuthUser()->shortName}}</p>
