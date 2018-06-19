<br/>
<p style='font-size: 1.3em;line-height: 200%'>
    A {{config('contacto.poblacion')}}, a {{$datosInforme['fecha']}}<br/><br/>
    Vist i plau
</p>
<div class="container" style="width:90%;font-size: 1.2em;line-height: 200%">
    <br/><br/><br/>
    <div style="width:50%; float:left; ">
        <p><strong>{{strtoupper($datosInforme['director']['articulo'].' '.$datosInforme['director']['genero']) }}</strong></p>
        <br/><br/><br/>
        <p>Signatura: {{$datosInforme['director']['nombre']}} </p>
    </div>
    <div style="width:50%; float:right; ">
        <p><strong>{{strtoupper($datosInforme['secretario']['articulo'].' '.$datosInforme['secretario']['genero'])}} </strong></p>
        <br/><br/><br/>
        <p>Signatura: {{$datosInforme['secretario']['nombre']}} </p>
    </div>
</div>
