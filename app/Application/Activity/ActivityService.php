<?php

declare(strict_types=1);

namespace Intranet\Application\Activity;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Activity;
use Styde\Html\Facades\Alert;

/**
 * Servei d'aplicació per al registre d'activitat d'usuari.
 *
 * Manté la lògica de creació/persistència i la notificació UI
 * fora del model Eloquent `Activity`.
 */
class ActivityService
{
    /**
     * Crea un registre d'activitat i, si hi ha usuari autenticat, el persistix associat a l'autor.
     *
     * @param string $action Acció registrada (`create`, `update`, `email`, ...).
     * @param Model|null $model Model afectat (opcional).
     * @param string|null $comentari Text lliure associat a l'acció.
     * @param string|null $fecha Data textual legacy (es transforma amb `fechaInglesaLarga`).
     * @param string|null $document Context documental opcional.
     * @return Activity
     */
    public function record(
        string $action,
        ?Model $model = null,
        ?string $comentari = null,
        ?string $fecha = null,
        ?string $document = null
    ): Activity {
        $activity = new Activity([
            'action' => $action,
            'comentari' => $comentari,
            'document' => $document,
            'model_class' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'created_at' => $fecha ? fechaInglesaLarga($fecha) : now(),
        ]);

        $user = auth()->user();
        if ($user) {
            $user->Activity()->save($activity);
        }

        $this->notifyUser($activity);

        return $activity;
    }

    /**
     * Mostra una alerta de confirmació quan el registre està vinculat a un model.
     */
    private function notifyUser(Activity $activity): void
    {
        if ($activity->model_class) {
            $modelName = trans('models.modelos.' . class_basename($activity->model_class));
            $message = trans("messages.generic.{$activity->action}");
            Alert::success("$modelName $message");
        }
    }
}
