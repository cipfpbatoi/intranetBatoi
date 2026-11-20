<!doctype html>
<html lang="ca">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Inici de sessió en NM</title>
    </head>
    <body>
        <main>
            <p>Estem completant l'inici de sessió en NM…</p>
            <form id="nm-sso-form" method="POST" action="{{ $callback }}" autocomplete="off">
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="state" value="{{ $state }}">
                <button type="submit">Continuar a NM</button>
            </form>
        </main>
        <script nonce="{{ $nonce }}">
            document.getElementById('nm-sso-form').submit();
        </script>
    </body>
</html>
