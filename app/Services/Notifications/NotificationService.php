<?php

namespace Intranet\Services\Notifications;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Alumno;
use Intranet\Notifications\mensajePanel;
use Illuminate\Support\Facades\Schema;

/**
 * Envia notificacions internes a alumnes i professorat.
 */
class NotificationService
{
    /** @var callable */
    private $findAlumno;
    /** @var callable */
    private $findProfesor;
    /** @var callable */
    private $hasTable;
    /** @var callable */
    private $fechaProvider;

    /**
     * Crea el servei amb resolutors substituïbles per a facilitar proves.
     *
     * @param callable|null $findAlumno
     * @param callable|null $findProfesor
     * @param callable|null $hasTable
     * @param callable|null $fechaProvider
     */
    public function __construct(
        ?callable $findAlumno = null,
        ?callable $findProfesor = null,
        ?callable $hasTable = null,
        ?callable $fechaProvider = null
    ) {
        $profesorService = app(ProfesorService::class);
        $this->findAlumno = $findAlumno ?? static fn ($id) => Alumno::find($id);
        $this->findProfesor = $findProfesor ?? static fn ($id) => $profesorService->find((string) $id);
        $this->hasTable = $hasTable ?? static fn (string $table) => Schema::hasTable($table);
        $this->fechaProvider = $fechaProvider ?? static fn () => fechaString();
    }

    /**
     * Resol el receptor a partir del seu NIA o DNI.
     *
     * @param string $id
     * @return mixed
     */
    private function receptor($id)
    {
        if (strlen($id) == 8) {
            if (!(($this->hasTable)('alumnos'))) {
                return null;
            }
            return ($this->findAlumno)($id);
        }
        if (!(($this->hasTable)('profesores'))) {
            return null;
        }
        return ($this->findProfesor)($id);
    }

    /**
     * Resol el text de l'emissor de la notificació.
     *
     * @param string|null $emisor
     * @return string
     */
    private function emisor($emisor)
    {
        if ($emisor) {
            return $emisor;
        }
        if (authUser()) {
            return authUser()->shortName;
        }
        if (apiAuthUser()) {
            return apiAuthUser()->shortName;
        }

        return 'Sistema';
    }

    /**
     * Normalitza una cadena o array de receptors a identificadors individuals.
     *
     * @param string|array $id
     * @return array<int, string>
     */
    private function recipients($id): array
    {
        $rawIds = is_array($id) ? $id : [$id];
        $recipients = [];

        foreach ($rawIds as $rawId) {
            foreach (explode(',', trim((string) $rawId, " \t\n\r\0\x0B[]")) as $recipient) {
                $recipient = trim($recipient);
                $recipient = trim($recipient, " \t\n\r\0\x0B'\"");
                if ($recipient !== '') {
                    $recipients[] = $recipient;
                }
            }
        }

        return array_values(array_unique($recipients));
    }

    /**
     * Envia una notificació interna a un o més receptors.
     *
     * @param string|array $id
     * @param string $mensaje
     * @param string $enlace
     * @param string|null $emisor
     * @return void
     */
    public function send($id, $mensaje, $enlace = '#', $emisor = null)
    {
        $ids = $this->recipients($id);
        $emisor = $this->emisor($emisor);
        $fecha = ($this->fechaProvider)();

        foreach (array_filter($ids) as $destinatari) {
            $receptor = $this->receptor($destinatari);
            if ($emisor && $receptor) {
                $receptor->notify(new mensajePanel(
                    ['motiu' => $mensaje,
                        'emissor' => $emisor,
                        'data' => $fecha,
                        'enlace' => $enlace]));
            } else {
                if ((($this->hasTable)('profesores')) && $user = ($this->findProfesor)($emisor)) {
                    $user->notify(new mensajePanel(
                        [
                            'motiu' => "No trobe usuari $destinatari",
                            'emissor' => $emisor,
                            'data' => $fecha,
                            'enlace' => $enlace
                        ]));
                }
            }
        }
    }
}
