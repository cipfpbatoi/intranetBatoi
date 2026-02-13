<?php

namespace Intranet\Services\School;

use Intranet\Entities\Expediente;
use Intranet\Services\General\StateService;

/**
 * Fluxos de negoci d'estat per a expedients.
 */
class ExpedienteWorkflowService
{
    /**
     * Autoritza en lot tots els expedients pendents (estat 1 -> 2).
     *
     * @return void
     */
    public function authorizePending(): void
    {
        StateService::makeAll(Expediente::where('estado', '1')->get(), 2);
    }

    /**
     * Inicialitza un expedient.
     *
     * - Si és d'orientació, passa a estat 4.
     * - En cas contrari, passa a estat 1.
     *
     * @param int|string $id
     * @return bool
     */
    public function init(int|string $id): bool
    {
        $expediente = Expediente::find($id);
        if (!$expediente) {
            return false;
        }

        $staSrv = new StateService($expediente);

        if (($expediente->tipoExpediente->orientacion ?? 0) >= 1) {
            $grupo = $expediente->Alumno?->Grupo?->first()?->nombre;
            $mensaje = trim(($expediente->explicacion ?? '') . ($grupo ? ' .Grup ' . $grupo : ''));
            $staSrv->putEstado(4, $mensaje);
            return true;
        }

        $staSrv->putEstado(1);
        return true;
    }

    /**
     * Passa l'expedient a orientació tancada (estat 5) i fixa data de solució.
     *
     * @param int|string $id
     * @return bool
     */
    public function passToOrientation(int|string $id): bool
    {
        $expediente = Expediente::find($id);
        if (!$expediente) {
            return false;
        }

        $staSrv = new StateService($expediente);
        $staSrv->putEstado(5);
        $expediente->fechasolucion = Hoy();
        $expediente->save();

        return true;
    }

    /**
     * Assigna professor acompanyant i passa l'expedient a estat 5.
     *
     * @param int|string $id
     * @param string|null $idAcompanyant
     * @return bool
     */
    public function assignCompanion(int|string $id, ?string $idAcompanyant): bool
    {
        $expediente = Expediente::find($id);
        if (!$expediente) {
            return false;
        }

        $staSrv = new StateService($expediente);
        $expediente->idAcompanyant = $idAcompanyant;
        $expediente->fechasolucion = Hoy();
        $expediente->save();

        $name = $expediente->Acompanyant?->fullName ?? '';
        $staSrv->putEstado(5, "Assignat professor Acompanyant $name");

        return true;
    }
}

