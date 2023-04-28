<ul>
    @foreach ($elemento->Alumnos as $alumno)
    <li> {{$alumno->fullName}} - {{$alumno->email}} - {{fechaString( $alumno->pivot->desde) }}</li>
    @endforeach
</ul>
