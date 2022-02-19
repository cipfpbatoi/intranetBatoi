
<div>Hola sóc {{AuthUser()->shortName}}.</div>
<div style="text-align: justify">T'escric per tal de confirmar la <strong>visita</strong> al centre de treball per fer el <strong>seguiment de les practiques FCT</strong> dels alumnes del {{config('contacto.nombre')}}.</div>
<div style="text-align: justify">L'horari que em quedat és <strong>{{$elemento->pivot->hora_ini}} del {{FechaString($mail->fecha)}}</strong></div>

<div>Salutacions cordials.</div>
