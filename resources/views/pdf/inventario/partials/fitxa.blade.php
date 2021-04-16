{!! QrCode::size(150)->generate(env('APP_URL','http://intranet.cipfpbatoi.es').'/inventario/'.$material->id.'/edit'); !!}
<p style="font-size: small">
    @isset ($material->nserieprov)
        <strong>{{ $material->nserieprov }}</strong><br/>
    @endisset
    {{ $material->descripcion }}
    @isset ($material->marca) <br/> {{$material->marca}} - {{$material->modelo}} @endisset
    <br/><strong>{{ $material->Espacios->descripcion }}</strong>
    <br/>{{FechaString($datosInforme[0])}}
</p>