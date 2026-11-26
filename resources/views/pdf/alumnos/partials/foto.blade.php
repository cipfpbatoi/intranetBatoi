<div style="float:left;width:15%;margin-bottom:5px;margin-right:5px;height:170px;page-break-inside:avoid;{{ $style??'' }}">
    <div style="width:125px;margin-left:20px;">
        <div style="width:125px;height:125px;margin-bottom:4px;border:thin #000 solid;">
            @if(!empty($elemento->foto))
                <img src="{{ public_path('/storage/fotos/'.$elemento->foto) }}" style="display:block;width:125px;height:125px;" alt="Foto" />
            @endif
        </div>
        <h6 style="margin:0;font-size:xx-small;line-height:12px;">{{$elemento->nombre}} <b>{{  $elemento->posicion??'' }}</b> </h6>
    </div>
</div>
