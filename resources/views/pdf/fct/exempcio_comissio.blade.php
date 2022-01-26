<div class="page" style="font-size:large;line-height: 2em;text-align: justify;">
    <br/>
    <h3 style="text-align: center;margin: 2cm">INFORME DE LA COMISSIÓ DE VALORACIÓ D'EXEMPCIÓ DE LA FCT DEL CICLE FORMATIU {{strtoupper($datosInforme['cicle']->vliteral)}}.</h3>
    <p style="margin: 1cm;">Reunida la comissió de valoració de les exempcions de la FCT del CIP d'FP Batoi del curs {{ curso() }} del cicle formatiu {{$datosInforme['cicle']->vliteral}},
        vista la documentació presentada per l'alumn{{genre($todos->Alumno,'e')}} <strong> {{$todos->Alumno->fullName}}</strong>  amb NIF </strong>{{$todos->Alumno->dni}}</strong>
        i, l'informe emés pel coordinador del cicle, aquesta comissió resol:
    </p>
    @if  ($todos->horas < 380)
        <h3 style="margin: 1cm;">CONCEDIR L'EXEMPCIÓ PARCIAL DEL MÒDUL SOL·LICITAT</h3>
        <p style="margin: 1cm;">
            S'aconsella que realitze un total de {{$datosInforme['cicle']->horasFct - $todos->horas }} hores per a completar la formació adequadament
        </p>
    @else
        <h3 style="margin: 1cm;">CONCEDIR L'EXEMPCIÓ TOTAL DEL MÒDUL SOL·LICITAT</h3>
    @endif

    <p style="text-align: right;margin: 2cm;  ">{{$datosInforme['poblacion']}}, {{$datosInforme['date']}} </p>
    <p style="margin: 4cm;"></p>
    <div style="margin: 25px; float:left; width: 200px; height: 150px; text-align: center;">
        <h4>President{{genre($datosInforme['director'])}}</h4><br/><br/>
        {{$datosInforme['director']->fullName}}
        <br/>
    </div>
    <div style="margin: 25px; float:left; width: 200px; height: 150px; text-align: center;">
        <h4>Vocal</h4><br/><br/>
        {{$datosInforme['cdept']->fullName}}
        <br/>
    </div>
    <div style="margin: 25px; float:left; width: 200px; height: 150px; text-align: center;">
        <h4>Vocal</h4><br/><br/>
        {{$datosInforme['tutor']->fullName}}
        <br/>
    </div>
</div>

