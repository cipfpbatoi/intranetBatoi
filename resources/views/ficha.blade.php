<!DOCTYPE html>
<html>
    <head><title>Fitxaje professorat</title></head>
    <body>
        @if ($ultimo)
            <h1>{{ $ultimo->Profesor->fullName }} fitxa
            @if (isset($ultimo->salida))
                l'eixida a les {{$ultimo->salida}} del dia {{ $ultimo->dia }}.ADEU !! Has fitxat en <a href="https://acces.edu.gva.es/sso/login.xhtml">Itaca</a> ??
            @else
                l'entrada a les {{$ultimo->entrada}} del dia {{ $ultimo->dia }}.HOLA !! Recorda fitxar en <a href="https://acces.edu.gva.es/sso/login.xhtml">Itaca</a>
            @endif
            </h1>
        @else
            @if ($ultimo === false)
                <h1> {{ trans('messages.generic.fueraCentro') }}</h1>
            @else
                <h1>{{ trans('messages.generic.acaba') }}</h1>
            @endif
        @endif
    </body>
</html>