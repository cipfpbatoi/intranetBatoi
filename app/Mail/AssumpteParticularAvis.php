<?php

declare(strict_types=1);

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\AssumpteParticularMailDelivery;

/** Correu informatiu d'una petició d'assumptes particulars. */
class AssumpteParticularAvis extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** Crea un correu a partir de l'estat de la petició confirmada. */
    public function __construct(
        public readonly AssumpteParticular $peticio,
        public readonly string $tipus
    ) {
    }

    /** Defineix un assumpte i una plantilla sense adjuntar el document privat. */
    public function build(): self
    {
        $assumpte = match ($this->tipus) {
            AssumpteParticularMailDelivery::URGENT => 'Petició urgent d’assumptes particulars',
            AssumpteParticularMailDelivery::DENEGADA => 'Petició d’assumptes particulars denegada',
            AssumpteParticularMailDelivery::AUTORITZADA => 'Petició d’assumptes particulars autoritzada',
            default => throw new \InvalidArgumentException('Tipus d’avís d’assumptes particulars desconegut.'),
        };

        return $this->subject($assumpte)->view('email.assumpteParticularAvis', [
            'tipus' => $this->tipus,
            'nomProfessor' => (string) ($this->peticio->profesor?->fullName ?? $this->peticio->idProfesor),
            'dataGaudi' => $this->peticio->data_gaudi->format('d/m/Y'),
            'motivacio' => (string) ($this->peticio->motivacio_excepcional ?? ''),
            'motiuDenegacio' => (string) ($this->peticio->resolucio ?? ''),
            'urlDocument' => $this->tipus === AssumpteParticularMailDelivery::AUTORITZADA
                ? route('assumptes-particulars.document', ['assumpteParticular' => $this->peticio->getKey()])
                : null,
        ]);
    }
}
