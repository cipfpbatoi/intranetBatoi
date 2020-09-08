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
            @if ($ultimo === false)
                <h1> {{ Alert::danger(trans('messages.generic.fueraCentro')) }}</h1>
            @else
                <h1>{{ Alert::danger(trans('messages.generic.acaba')) }}</h1>
            @endif
        @endif
    </body>
</html>