<div class="container" style="width:90%;">
    <br/><br/>
    <p><strong>I para que así conste donde convenga, firmo el presente escrito.</strong></p>

    <p>{{$datosInforme['poblacion']}}, {{$datosInforme['date']}} </p>
    <br/><br/>
    <div style="width:40%; float:left;  ">
        <img src="{{public_path('img/pdf/secretari-director.png')}}"  width="800px" /><br/>
    </div>
    <div style="width:35%; float:left; clear: both;margin-right: 100px">
        <strong>{{$datosInforme['secretario']}}</strong><br/>
        @if ($datosInforme['consideracion'] == 'En') SECRETARIO @else SECRETARIA @endif
    </div>
    <div style="width:35%; float:left;margin-left: 150px ">
        <strong>{{$datosInforme['director']}}</strong><br/>
        Conforme {{signatura('certificado')}}
    </div>
</div>