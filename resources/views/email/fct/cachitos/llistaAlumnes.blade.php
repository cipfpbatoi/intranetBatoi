<ul>
    @foreach ($elemento->AlumnosActivos as $alumno)
        <li> {{$alumno->fullName}} </li>
    @endforeach
</ul>
