<div style="float:left;width:23%;margin-right:10px;height:160px;{{ $style??'' }}"    >
    @if ($elemento->foto)
        <img src="{{public_path($elemento->foto)}}" style="width:125px;height:125px;margin-left:20;border:thin #000 solid;" /><br/>
    @else
        <img src="" style="width:125px;height:125px;margin-left:20;border:thin #000 solid;" /><br/>
    @endif
    <h6 style="font-size:xx-small;text-align: center">{{$elemento->nombre.' '.$elemento->apellido1.' '.$elemento->apellido2}} <b>{{  $elemento->posicion??'' }}</b> </h6>
</div>