<div style="float:right;width: 45%;margin:2%">
{!! QrCode::size(120)->generate(env('APP_URL','http://intranet.cipfpbatoi.es').'/inventario/'.$material->id.'/edit'); !!}
</div>
<div style="float:left;width: 45%;margin:2%">
    <p style="font-size: small">
        @isset ($material->nserieprov)
            <strong>Serie: {{ $material->nserieprov }}</strong><br/>
        @endisset
        <strong>Id: {{ $material->id }}</strong><br/>
        {{ $material->descripcion }}<br/>
        @isset ($material->marca) <br/> {{$material->marca}} - {{$material->modelo}} @endisset
        <br/> {{$material->LoteArticulo->Lote->proveedor }}
        <br/>{{FechaString($material->LoteArticulo->Lote->fecha_alta)}}
    </p>
</div>