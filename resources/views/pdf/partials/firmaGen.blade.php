<div class="container col-lg-12" style="width:95%;text-align: justify">
    <div>{{$title}}</div>
    <br/><br/>
    <div style="width:40%; float:left; margin-left: 5%; ">
        <img src="{{url('img/pdf/'.imgSig($signatura).'.png')}}"  /><br/>
    </div>
    <div style="width:50%;float:left">SIGNAT {{signatura($signatura)}}:</div>
    <div style="width:50%;float:right;text-align: right">{{strtoupper(config('contacto.poblacion'))}} A
        @if (isset($fecha)) {{$fecha}} @else {{ $datosInforme }} @endif
    </div>
</div>