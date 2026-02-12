<?php

namespace Intranet\Services\Notifications;

use Illuminate\Support\Collection;
use Intranet\Entities\Actividad;
use Intranet\Entities\Profesor;

/**
 * Servei d'enviament de notificacions relacionades amb activitats.
 *
 * Centralitza:
 * - avisos al professorat dels grups afectats,
 * - avisos als professors participants de la pròpia activitat.
 */
class ActividadNotificationService
{
    /**
     * @var callable
     */
    private $groupTeachersResolver;
    /**
     * @var callable
     */
    private $adviseTeacherExecutor;

    public function __construct(
        private ?NotificationService $notificationService = null,
        ?callable $groupTeachersResolver = null,
        ?callable $adviseTeacherExecutor = null
    ) {
        $this->notificationService = $notificationService ?? app(NotificationService::class);
        $this->groupTeachersResolver = $groupTeachersResolver
            ?? static fn (string $groupCode): Collection => Profesor::Grupo($groupCode)->get();
        $this->adviseTeacherExecutor = $adviseTeacherExecutor
            ?? static fn (object $actividad, string $mensaje, string $dni, mixed $emisor): mixed
                => app(AdviseTeacher::class)->advise($actividad, $mensaje, $dni, $emisor);
    }

    /**
     * Envia notificacions a professorat de grups i participants.
     */
    public function notifyActivity(Actividad $actividad, Profesor $coordinador): void
    {
        $this->notifyGroups($actividad, $coordinador);
        $this->notifyParticipants($actividad);
    }

    /**
     * Envia missatge als professors dels grups inclosos en l'activitat.
     */
    private function notifyGroups(Actividad $actividad, Profesor $coordinador): void
    {
        foreach ($actividad->grupos as $grupo) {
            $mensaje = "El grup {$grupo->nombre} se’n va a l’activitat {$actividad->name}.";
            $profesores = call_user_func($this->groupTeachersResolver, $grupo->codigo);
            foreach ($profesores as $profesor) {
                $this->notificationService->send($profesor->dni, $mensaje, '#', $coordinador->shortName);
            }
        }
    }

    /**
     * Envia avís als professors participants de la pròpia activitat.
     */
    private function notifyParticipants(Actividad $actividad): void
    {
        $mensaje = "Els grups: " . $actividad->grupos->implode('nombre', ', ') .
            " van a l’activitat {$actividad->name} i jo me’n vaig amb ells. " .
            "Estarem fora des de {$actividad->desde} fins {$actividad->hasta}.";

        foreach ($actividad->profesores as $profesor) {
            call_user_func($this->adviseTeacherExecutor, $actividad, $mensaje, $profesor->dni, $profesor->shortName);
        }
    }
}
