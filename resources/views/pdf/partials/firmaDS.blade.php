<br/>
<p style='font-size: 1.2em;line-height: 175%'>
    A {{config('contacto.poblacion')}}, a {{$datosInforme['fecha']}}<br/><br/>
    Vist i plau
</p>
<div class="container" style="width:90%;font-size: 1.2em;line-height: 100%">
    <br/><br/>
    <div style="width:40%; float:left; margin-left: 0%; ">
        <img src="{{public_path('img/pdf/director-secretari.png')}}"  /><br/>
    </div>
    <div style="width:35%; float:left; clear: both;margin-left: 0%;margin-right: 25%;">
        <p><strong>{{strtoupper($datosInforme['director']['articulo'].' '.$datosInforme['director']['genero']) }}</strong></p>
        <p>Signatura: {{$datosInforme['director']['nombre']}} </p>
    </div>
    <div style="width:40%; float:left;margin-right: 0% ">
        <p><strong>{{strtoupper($datosInforme['secretario']['articulo'].' '.$datosInforme['secretario']['genero'])}} </strong></p>
        <p>Signatura: {{$datosInforme['secretario']['nombre']}} </p>
    </div>
</div>
