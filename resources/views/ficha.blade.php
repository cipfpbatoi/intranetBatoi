<!DOCTYPE html>
<html>
    <head><title>Fitxaje professorat</title></head>
    <body>
        @if ($ultimo)
            <h1>{{ $ultimo->Profesor->fullName }} fitxa
            @if (isset($ultimo->salida))
                l'eixida a les {{$ultimo->salida}}. ADEU !!
            @else
                l'entrada a les {{$ultimo->entrada}}. HOLA !!
            @endif
            </h1>
        @else
            <h1>Acabes de fitxar</h1>
        @endif
    </body>
</html>