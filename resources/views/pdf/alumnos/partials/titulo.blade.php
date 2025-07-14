@include('pdf.partials.cabecera')
<br/><br/>
<p style='font-size: 1.2em;line-height: 175%;text-align: justify'>
    {{$datosInforme['secretario']['titulo']}} {{$datosInforme['secretario']['nombre']}}, {{$datosInforme['secretario']['genero']}} del {{config('contacto.nombre')}} de {{config('contacto.poblacion')}}, que
    impartix el {{$datosInforme['ciclo']->Xtipo}} {{str_replace('TÈCNIC EN ','',str_replace('TÈCNIC SUPERIOR EN ','',$datosInforme['ciclo']->titol))}} corresponent al títol de {{$datosInforme['ciclo']->titol}},
    segons el Reial Decret {{$datosInforme['ciclo']->rd}}
    @if (isset($datosInforme['ciclo']->rd2)) i {{$datosInforme['ciclo']->rd2}} @endif
</p>
<h3 style="text-align: center">ACREDITE:</h3>
<br/>
