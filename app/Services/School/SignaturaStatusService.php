<?php

declare(strict_types=1);

namespace Intranet\Services\School;

use Intranet\Entities\Signatura;

class SignaturaStatusService
{
    public function estat(Signatura $sig): string
    {
        $tipus = substr((string) $sig->tipus, 0, 2);

        return match ($tipus) {
            'A1' => $this->estatA1($sig),
            'A2' => $this->estatA2($sig),
            'A3' => $this->estatA3($sig),
            'A5' => 'Complet',
            default => 'Tipus desconegut',
        };
    }

    public function cssClass(Signatura $sig): string
    {
        if (substr((string) $sig->tipus, 0, 2) === 'A3' && (int) $sig->sendTo === 1 && (int) $sig->signed === 2) {
            return 'bg-orange';
        }

        if ((int) $sig->signed >= 3) {
            return (int) $sig->sendTo >= 1 ? 'bg-blue-sky' : 'bg-green';
        }

        return 'bg-red';
    }

    public function yesNo(bool|int $value): string
    {
        return $value ? 'Sí' : 'No';
    }

    private function estatA1(Signatura $sig): string
    {
        if ($sig->sendTo) {
            return "Enviat a l'instructor";
        }
        if ((int) $sig->signed === 3) {
            return 'Signatura Direcció completada';
        }

        return 'Pendent Signatura Direcció';
    }

    private function estatA2(Signatura $sig): string
    {
        if ($sig->sendTo) {
            return "Enviat a l'instructor";
        }
        if ((int) $sig->signed > 2) {
            return 'Signatura Direcció completada';
        }

        return 'Pendent de Signatura Direcció';
    }

    private function estatA3(Signatura $sig): string
    {
        if ((int) $sig->sendTo > 0) {
            if ((int) $sig->signed === 3) {
                return "Enviat a l'instructor";
            }
            if ((int) $sig->signed === 2 && (int) $sig->sendTo === 2) {
                return "Enviat a l'instructor sense la signatura de l'alumne";
            }

            return "Enviat a l'alumne";
        }

        if ((int) $sig->signed === 2) {
            return "Pendent enviar a l'alumne";
        }

        return "Pendent enviar a l'instructor";
    }
}

