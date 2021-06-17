<div class="container">
    <br/>
    <div style="width:50%;float:left">
    <strong>Assistents:</strong>
    <ul style='list-style:none'>
        @foreach ($datosInforme->profesores as $profesor)
            @if ($profesor->pivot->asiste == 1)
                <li>{{$profesor->nombre}} {{$profesor->apellido1}} {{$profesor->apellido2}}</li>
            @endif
        @endforeach
    </ul>
    </div>
    <div style="width:50%;float:right">
    <strong>Absents:</strong>
    <ul style='list-style:none'>
        @foreach ($datosInforme->profesores as $profesor)
            @if ($profesor->pivot->asiste == 0)
                <li>{{$profesor->nombre}} {{$profesor->apellido1}} {{$profesor->apellido2}}</li>
            @endif
        @endforeach
    </ul>
    </div>
</div>