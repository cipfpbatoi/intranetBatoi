<div>T'escric per conèixer de primera ma com van les pràctiques FCT dels alumnes:
    <ul>
        @foreach ($elemento->Alumnos as $alumno)
            <li> {{$alumno->fullName}} </li>
        @endforeach
    </ul>
</div>
<div>Si tot està correcte et tornaria a contactar en aproximadament 15 dies per a fer una visita al centre de treball.
</div>
<div>Aprofite per recordar-te les meues dades per si necessites possar-te amb contacte amb mi:<br/>
    Tutor: {{authUser()->fullName}} {{authUser()->email}} <br/>
    Telèfon centre: {{ config('contacto.telefono') }} <br/>
</div>
<div>Per qualsevol dubte em tens a la teua disposició</div>
<div>Salutacions cordials de {{authUser()->shortName}}</div>

