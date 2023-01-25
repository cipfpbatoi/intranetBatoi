@if ($material)
        <div style="float:left; width: 3cm">
        {!! QrCode::size(115)
                ->generate(env('APP_URL','http://intranet.cipfpbatoi.es').'/inventario/'.$material->id.'/edit'); !!}
        </div>
        <div style="float:left; width: 4.90cm; font-size: medium; margin-left: 0.3cm;text-align: left">
                <strong>{{$material->espacio}}</strong><br/>
                <strong>Id: {{ $material->id }}</strong>
                    @if (($material->nserieprov) && ($material->nserieprov != 'null' ))
                        ({{ $material->nserieprov }})
                    @endif
                <br/>
                {{ ucfirst(strtolower(substr($material->descripcion,0,22))) }}<br/>
                @if (($material->marca) && (strlen($material->marca)>0))
                        {{ucfirst(strtolower($material->marca))}}
                        <br/>
                @endif
                @if (($material->modelo) && (strlen($material->modelo)>0))
                        ({{ucfirst(strtolower($material->modelo))}}) <br/>
                @endif
                {{ ucfirst(strtolower(substr($material->proveedor?$material->proveedor:($material->LoteArticulo?$material->LoteArticulo->Lote->proveedor:''),0,22))) }}
        </div>
@endif
