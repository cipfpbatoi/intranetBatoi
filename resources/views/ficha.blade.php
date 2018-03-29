<!DOCTYPE html>
<html>
    <head><title>Fichaje profesor</title></head>
    <body>
        <h1>{{ $ultimo->profesor }} fitxa  
        @if (isset($ultimo->salida)) 
            l'eixida a les {{$ultimo->salida}}. ADEU !!
        @else
            l'entrada a les {{$ultimo->entrada}}. HOLA !!
        @endif
        </h1>
    </body>
</html>