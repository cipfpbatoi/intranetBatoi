<br/>
<p>{{$title}}</p>
<div style="width:40%; float:left; margin-left: 5%; ">
    <img src="{{url('img/pdf/'.imgSig($signatura).'.png')}}"  /><br/>
</div>
<div class="container col-lg-12">
    <div style="width:50%;float:left">SIGNAT {{signatura($signatura)}}:</div>
    <div style="width:50%;float:right;text-align: right">{{strtoupper(config('contacto.poblacion'))}} A {{ $datosInforme }}</div>
</div>