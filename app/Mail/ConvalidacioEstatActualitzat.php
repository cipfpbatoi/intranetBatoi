<?php

declare(strict_types=1);

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Notifica a l'alumnat el canvi d'estat d'una petició individual. */
class ConvalidacioEstatActualitzat extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $nomAlumne Nom de l'alumne destinatari.
     * @param string $modul Nom del mòdul afectat.
     * @param string $estat Etiqueta de l'estat nou.
     * @param string|null $observacions Comentari de Direcció, si n'hi ha.
     */
    public function __construct(
        public string $nomAlumne,
        public string $modul,
        public string $estat,
        public ?string $observacions
    ) {
    }

    /** Construïx el correu sense adjuntar la documentació privada de la petició. */
    public function build(): static
    {
        return $this
            ->subject('Actualització d’una petició de convalidació')
            ->view('email.convalidacions.estat-modul');
    }
}
