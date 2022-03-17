<div style="float:left; width: 3cm">
{!! QrCode::size(115)->generate(env('APP_URL','http://intranet.cipfpbatoi.es').'/inventario/'.$material->id.'/edit'); !!}
</div>
<div style="float:left; width: 4.90cm; font-size: medium; margin-left: 0.3cm;text-align: left">
        <strong>Id: {{ $material->id }}</strong>
            @if (($material->nserieprov) && ($material->nserieprov != 'null' ))
                ({{ $material->nserieprov }})
            @endif
        <br/>
        {{ ucfirst(strtolower(substr($material->descripcion,0,22))) }}<br/>
        @isset ($material->marca)
                {{ucfirst(strtolower($material->marca))}}
                @isset ($material->modelo)
                        ({{ucfirst(strtolower($material->modelo))}})
                @endisset
                <br/>
        @endisset
        {{ ucfirst(strtolower(substr($material->LoteArticulo->Lote->proveedor,0,22))) }}
</div>