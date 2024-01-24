<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Correu automàtic</title>
        <link rel="stylesheet" href="{{public_path('/css/email.css')}}" >
        @yield('css')
    </head>
 @yield('body')
    <footer>
        <p>Assumpte: Instruccions Importants per al Reenviament de Correus.
        Benvolgut/da destinatari/ària, si necessites <strong>reenviar este correu</strong>  a una altra persona,
            et preguem que l'informes que l'adreça original és
            <strong>{{authUser()->email??'i.gomismur@edu.gva.es'}}</strong>,
            i a esta és on ha d'adreçar la respota.
        Atentament, l'equip de suport de {{config('contacto.nombre')}}</p>
    </footer>
</html>
