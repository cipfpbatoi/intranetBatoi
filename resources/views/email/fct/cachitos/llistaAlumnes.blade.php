<ul>
    @foreach ($elemento->Alumnos as $alumno)
        <li> {{$alumno->fullName}} </li>
    @endforeach
</ul>
