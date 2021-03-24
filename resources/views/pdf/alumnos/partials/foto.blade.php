<div style="float:left;width:23%;margin-bottom:5px;margin-right:10px;height:160px;{{ $style??'' }}"    >
    <img src="{{$elemento->foto?public_path('/storage/'.$elemento->foto):''}}" style="width:125px;height:125px;margin-left:20;border:thin #000 solid;" /><br/>
    <h6 style="margin-top:-1px;clear:both;font-size:xx-small;">{{$elemento->nombre}} <b>{{  $elemento->posicion??'' }}</b> </h6>
</div>