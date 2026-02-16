<?php

declare(strict_types=1);

namespace Intranet\Application\AlumnoFct;

use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura;

/**
 * Casos d'ús de signatures vinculades a AlumnoFct.
 */
class AlumnoFctSignatureService
{
    /**
     * Determina si el registre té alguna signatura associada.
     */
    public function hasAnySignature(AlumnoFct $alumnoFct): bool
    {
        if ($alumnoFct->relationLoaded('Signatures')) {
            return $alumnoFct->Signatures->isNotEmpty();
        }

        if (empty($alumnoFct->idSao)) {
            return false;
        }

        return $alumnoFct->Signatures()->exists();
    }

    /**
     * Cerca la signatura per tipus i estat de signatura.
     */
    public function findByType(AlumnoFct $alumnoFct, string $tipus, ?bool $signed = null): ?Signatura
    {
        if (empty($alumnoFct->idSao)) {
            return null;
        }

        if ($alumnoFct->relationLoaded('Signatures')) {
            $query = $alumnoFct->Signatures->where('tipus', $tipus);
            if ($signed !== null) {
                $query = $query->where('signed', $signed);
            }

            return $query->first();
        }

        $query = Signatura::query()
            ->where('idSao', $alumnoFct->idSao)
            ->where('tipus', $tipus);

        if ($signed !== null) {
            $query->where('signed', $signed);
        }

        return $query->first();
    }

    /**
     * Construeix la ruta física de l'annex per al registre.
     */
    public function routeFile(AlumnoFct $alumnoFct, string $annexCode): string
    {
        $prefix = strlen($annexCode) > 1 ? $annexCode : "A{$annexCode}";
        return storage_path("app/annexes/{$prefix}_{$alumnoFct->idSao}.pdf");
    }
}

