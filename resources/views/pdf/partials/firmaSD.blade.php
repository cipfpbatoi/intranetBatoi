<div class="container" style="width:90%;">
    <br/><br/>
    <p><strong>I per tal que així conste on convinga, signa el present escrit.</strong></p>

    <p>{{$datosInforme['poblacion']}}, {{$datosInforme['date']}} </p>
    <br/><br/>
    <div style="width:40%; float:left; margin-left: 12%; ">
        <img src="{{public_path('img/pdf/secretari-director.png')}}"  /><br/>
    </div>
    <div style="width:35%; float:left; clear: both;margin-left: 6%">
        <p><strong>{{$datosInforme['secretario']}}</strong></p>
        <p>@if ($datosInforme['consideracion'] == 'En') SECRETARI @else SECRETARIA @endif</p>
    </div>
    <div style="width:35%; float:left;margin-left: 15% ">
        <p><strong>{{$datosInforme['director']}}</strong></p>
        <p>Vist-i-plau {{signatura('certificado')}}</p>
    </div>
</div>