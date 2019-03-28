@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th>A/A Departaments d'Informàtica i de Recursos Humans</th>
        </tr>
    </table>
    <div>
        <table style=" border:#000 solid 1;">
            <tr >
                <td><strong>De {{AuthUser()->shortName}} del {{config('contacto.nombre')}} </strong></td>
            </tr>
        </table>
    </div>
    <div class="container" >
            <p>Hola,</p>
            <p>El meu nom és {{AuthUser()->shortName}} i sóc el professor-tutor del  {{config('auxiliares.tipoEstudio.'.$elemento->ciclo->tipo)}} '{{$elemento->ciclo->literal}}'</b> del {{config('contacto.nombre')}}.<br/>
            <p>Les classes de segon curs acaben a principis de març, i després, els alumnes han de fer 400 hores de pràctiques en empreses/organitzacions/entitats/etc, amb l'horari normal de l'empresa (que sol ser 40 hores setmanals).</p>
            <p>Com tots els anys, estem buscant llocs de pràctiques per als nostres alumnes i hem pensat que potser la vostra empresa podria acollir les pràctiques d'un dels alumnes.</p>
            <p>Actualment, tenim alumnes que estarien molt interessats en fer les seues pràctiques en una empresa com la vostra, que tinga almenys un tècnic</p>
            <p>Per tot això, ens agradaria que consideràreu la possibilitat d'acollir les pràctiques d'un dels nostres alumnes entre el 11 de març i el 30 de maig, aproximadament.</p>
            <p>Òbviament, abans de prendre la vostra decisió, parlaríem tot allò que fera falta i també podríeu entrevistar als alumnes candidats.</p>
            <p>En qualsevol cas, moltes gràcies per considerar la nostra sol·licitud.</p>
            <p>Salutacions cordials de {{AuthUser()->shortName}}</p>
    </div>
@endsection