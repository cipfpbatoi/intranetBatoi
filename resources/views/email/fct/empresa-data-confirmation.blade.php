@extends('layouts.email')

@section('body')
    <p>Bon dia,</p>
    <p>
        En primer lloc, volem agrair-vos sincerament la vostra col·laboració amb el CIPFP Batoi i
        la important tasca que realitzeu en la formació del nostre alumnat.
    </p>
    <p>
        A causa del canvi en l’aplicació de gestió de les pràctiques, necessitem revisar i completar les dades
        de <strong>{{ $confirmation->empresa->nombre }}</strong>, dels seus centres de treball i dels instructors.
        També cal designar un dels instructors com a coordinador o coordinadora de l’empresa.
    </p>
    <p>
        El formulari ja conté les dades disponibles al centre, de manera que només haureu de comprovar-les
        i corregir-les si és necessari. Vos agrairíem que l’emplenàreu mitjançant l’enllaç següent:
    </p>
    <p style="text-align: center; margin: 24px 0;">
        <a href="{{ $url }}" style="background:#0d6efd;color:#fff;padding:12px 20px;text-decoration:none;border-radius:4px;">
            Revisar i confirmar les dades
        </a>
    </p>
    <p>L’enllaç és personal, només mostra esta sol·licitud i caduca el {{ $confirmation->expires_at->format('d/m/Y') }}.</p>
    <p>
        Posteriorment enviarem al coordinador o coordinadora la informació necessària per a connectar-se a la
        plataforma i gestionar el procés de confirmació de les pràctiques.
    </p>
    <p>Moltes gràcies per la vostra dedicació i col·laboració.</p>
    <p>Salutacions cordials,<br>{{ $tutorName }}<br>CIPFP Batoi</p>

    <hr style="margin: 32px 0;">

    <p>Buenos días:</p>
    <p>
        En primer lugar, queremos agradecerles sinceramente su colaboración con el CIPFP Batoi y
        la importante labor que realizan en la formación de nuestro alumnado.
    </p>
    <p>
        Debido al cambio en la aplicación de gestión de las prácticas, necesitamos revisar y completar los datos
        de <strong>{{ $confirmation->empresa->nombre }}</strong>, de sus centros de trabajo y de los instructores.
        También deberán designar a uno de los instructores como coordinador o coordinadora de la empresa.
    </p>
    <p>
        El formulario ya contiene los datos disponibles en el centro, por lo que únicamente tendrán que
        comprobarlos y corregirlos si fuera necesario. Les agradeceríamos que lo completaran mediante el siguiente enlace:
    </p>
    <p style="text-align: center; margin: 24px 0;">
        <a href="{{ $url }}" style="background:#0d6efd;color:#fff;padding:12px 20px;text-decoration:none;border-radius:4px;">
            Revisar y confirmar los datos
        </a>
    </p>
    <p>El enlace es personal, solo muestra esta solicitud y caduca el {{ $confirmation->expires_at->format('d/m/Y') }}.</p>
    <p>
        Posteriormente enviaremos al coordinador o coordinadora la información necesaria para conectarse a la
        plataforma y gestionar el proceso de confirmación de las prácticas.
    </p>
    <p>Muchas gracias por su dedicación y colaboración.</p>
    <p>Atentamente,<br>{{ $tutorName }}<br>CIPFP Batoi</p>
@endsection
