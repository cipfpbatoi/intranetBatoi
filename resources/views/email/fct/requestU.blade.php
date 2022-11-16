<div>Estic preparant la documentació corresponent a les pràctiques de FCT del
    {{config('auxiliares.tipoEstudio.'.$elemento->ciclo->tipo)}} '{{$elemento->ciclo->literal}}' ,
    i necessitaria que em confirmàreu els següents detalls de la documentació oficial:<br/>
    <ul>
        <li>Empresa: {{$elemento->Centro->nombre }}</li>
        <li>CIF: {{$elemento->Centro->Empresa->cif}}</li>
        <li>Telèfon: {{$elemento->telefono}}</li>
        <li>Adreça: {{$elemento->Centro->direccion}}</li>
        <li>Poble: {{$elemento->Centro->localidad}}</li>
        <li>Email-Empresa : {{$elemento->centro->email}}</li>
        <li>Horari Pràctiques: {{$elemento->Centro->horarios}}</li>
    </ul>
    @if (!isset($elemento->Centro->Empresa->concierto))
        <ul>
            <li>Representant legal: ____________________________________________________</li>
            <li>DNI Representant legal: _________________________________________________</li>
        </ul>
    @endif
</div>
<div>Tria Instructor:</div>
<div>
    @foreach ($elemento->Centro->Instructores as $instructor)
        <ul>
            <li>Nom: {{$instructor->nombre}}</li>
            <li>E-mail: {{$instructor->email}}</li>
            <li>DNI : {{$instructor->dni}}</li>
            <li>Telèfon: {{$instructor->telefono}}</li>
        </ul>
    @endforeach
</div>

<div>O afegueix un altre:</div>
<div>
    <ul>
        <li>Nom: ___________________________________________</li>
        <li>E-mail : ____________________________________</li>
        <li>DNI : _____________________________________</li>
        <li>Telèfon: __________________________________</li>
    </ul>
</div>
<div>Salutacions cordials de {{authUser()->shortName}}</div>