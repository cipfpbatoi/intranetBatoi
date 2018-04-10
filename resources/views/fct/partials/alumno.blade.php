@foreach($fct->Alumno->getVisible() as $campo)
    <li><p> {{ trans("validation.attributes.$campo") }} : {{ $fct->Alumno->$campo }}</p></li>
@endforeach

