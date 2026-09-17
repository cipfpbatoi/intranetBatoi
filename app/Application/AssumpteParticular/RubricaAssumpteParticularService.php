<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Profesor;

/**
 * Resol de manera segura la rúbrica gràfica vinculada al perfil d'un professor.
 */
class RubricaAssumpteParticularService
{
    /**
     * Retorna la ruta absoluta de la rúbrica o impedix continuar si no existix.
     */
    public function path(Profesor $professor, string $subjecte = 'professor'): string
    {
        $nom = basename((string) ($professor->foto ?? ''));
        $ruta = $nom !== '' ? 'signatures/' . $nom : '';

        if ($ruta === '' || !Storage::disk('public')->exists($ruta)) {
            throw new AssumpteParticularException(
                sprintf(
                    'El %s no té una rúbrica guardada en el perfil. Cal pujar-la des de Fitxers del perfil.',
                    $subjecte
                )
            );
        }

        return Storage::disk('public')->path($ruta);
    }

    /**
     * Indica si el professor té una rúbrica disponible.
     */
    public function exists(Profesor $professor): bool
    {
        $nom = basename((string) ($professor->foto ?? ''));

        return $nom !== '' && Storage::disk('public')->exists('signatures/' . $nom);
    }
}
