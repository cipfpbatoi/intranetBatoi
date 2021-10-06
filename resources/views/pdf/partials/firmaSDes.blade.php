<div class="container" style="width:90%;">
    <br/><br/>
    <p><strong>I para que así conste donde convenga, firmo el presente escrito.</strong></p>

    <p>{{$datosInforme['poblacion']}}, {{$datosInforme['date']}} </p>
    <br/><br/>
    <div style="width:40%; float:left;  ">
        <img src="{{public_path('img/pdf/secretari-director.png')}}"  width="800px" /><br/>
    </div>
    <div style="width:35%; float:left; clear: both;margin-right: 100px">
        <p><strong>{{$datosInforme['secretario']}}</strong></p>
        <p>@if ($datosInforme['consideracion'] == 'En') SECRETARIO @else SECRETARIA @endif</p>
    </div>
    <div style="width:35%; float:left;margin-left: 150px ">
        <p><strong>{{$datosInforme['director']}}</strong></p>
        <p>Conforme {{signatura('certificado')}}</p>
    </div>
</div>