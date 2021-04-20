{!! QrCode::size(175)->generate(env('APP_URL','http://intranet.cipfpbatoi.es').'/inventario/'.$material->id.'/edit'); !!}
<p style="font-size: small">
    @isset ($material->nserieprov)
        <strong>{{ $material->nserieprov }}</strong><br/>
    @endisset
    <strong>{{ $material->id }} </strong>{{ $material->descripcion }}
    @isset ($material->marca) <br/> {{$material->marca}} - {{$material->modelo}} @endisset
    <br/>{{FechaString($datosInforme[0])}}
</p>