<div style="text-align: justify">El meu nom és {{authUser()->fullName}} i sóc el professor-tutor
    del {{config('auxiliares.tipoEstudio.'.$elemento->ciclo->tipo)}}<strong> {{$elemento->ciclo->literal}} </strong>
    del {{config('contacto.nombre')}}.
</div>
<div>Les classes de segon curs acaben a principis de març, i després, els alumnes han de fer
    <strong>{{$elemento->ciclo->horasFct}} hores</strong> de pràctiques en empreses, organitzacions, entitats ..., en
    l'horari normal de l'empresa (que sol ser 40 hores setmanals).
    Com tots els anys, estem buscant llocs de pràctiques per als nostres alumnes i hem pensat que potser la vostra
    empresa podria acollir les pràctiques d'un dels alumnes.
    Actualment, tenim alumnes que estarien molt interessats en fer les seues pràctiques en una empresa com la vostra.
    L'unic requeriment es dispossar d'almenys un tècnic, que puga tutoritzar les pràctiques en l'empressa.
    Per tot això, ens agradaria que consideràreu la possibilitat d'acollir les pràctiques d'un dels nostres alumnes
    entre el 11 de març i el 10 de juny, aproximadament.
</div>
<div style="text-align: justify">Òbviament, abans de prendre la vostra decisió, parlaríem tot allò que fera falta i
    també podríeu entrevistar als alumnes candidats.
    En qualsevol cas, moltes gràcies per considerar la nostra sol·licitud.
</div>
<div>Salutacions cordials de {{authUser()->shortName}}</div>
[peu]
