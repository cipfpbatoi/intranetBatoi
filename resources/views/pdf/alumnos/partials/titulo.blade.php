@include('pdf.partials.cabecera')
<br/><br/>
<p style='font-size: 1.3em;line-height: 200%;text-align: justify'>
    {{$datosInforme['secretario']['titulo']}} {{$datosInforme['secretario']['nombre']}}, {{$datosInforme['secretario']['genero']}} del {{config('constants.contacto.nombre')}} de {{config('constants.contacto.poblacion')}}, que
    impartix el {{$datosInforme['ciclo']->Xtipo}} {{str_replace('TÈCNIC EN ','',str_replace('TÈCNIC SUPERIOR EN ','',$datosInforme['ciclo']->titol))}} corresponent al títol de {{$datosInforme['ciclo']->titol}}, segons el Reial Decret
    {{$datosInforme['ciclo']->rd}}
    @if (isset($datosInforme['ciclo']->rd2)) i el Reial Decret {{$datosInforme['ciclo']->rd2}} @endif 
</p>
<h2 style="text-align: center">ACREDITE:</h2>
<br/><br/>
