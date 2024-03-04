<div style="float:left;width:15%;margin-bottom:5px;margin-right:5px;height:140px;{{ $style??'' }}"    >
    <img src="{{$elemento->foto?public_path('/storage/fotos/'.$elemento->foto):''}}" style="width:125px;height:125px;margin-left:20;border:thin #000 solid;" /><br/>
    <h6 style="margin-top:-1px;clear:both;font-size:xx-small;">{{$elemento->nombre}} <b>{{  $elemento->posicion??'' }}</b> </h6>
</div>