<?php

declare(strict_types=1);

namespace Intranet\Application\Falta;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Falta;
use Intranet\Services\General\StateService;
use Intranet\Services\Notifications\AdviseTeacher;
use Intranet\Services\School\TeacherSubstitutionService;
use Illuminate\Support\Carbon;

/**
 * Casos d'ús d'aplicació per al domini de faltes de professorat.
 */
class FaltaService
{
    private const MOTIU_ASSUMPTE_PARTICULAR = 'PERMÍS RETRIBUÏT PER ASSUMPTES PARTICULARS';

    /**
     * Crea una falta de professorat.
     */
    public function create(Request $request): int
    {
        $request->merge(['baja' => $request->boolean('baja') ? 1 : 0]);

        if ($request->baja) {
            return DB::transaction(function () use ($request): int {
                $request->merge([
                    'hora_ini' => null,
                    'hora_fin' => null,
                    'hasta' => '',
                    'dia_completo' => 1,
                    'estado' => 5,
                ]);

                app(TeacherSubstitutionService::class)->markLeave((string) $request->idProfesor, (string) $request->desde);

                $falta = new Falta();
                return (int) $falta->fillAll($request);
            });
        }

        $diaCompleto = $request->boolean('dia_completo') ? 1 : 0;
        $request->hora_ini = $diaCompleto ? null : $request->hora_ini;
        $request->hora_fin = $diaCompleto ? null : $request->hora_fin;
        $request->hasta = esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta;

        $falta = new Falta();
        return (int) $falta->fillAll($request);
    }

    /**
     * Crea la falta autoritzada de dia complet vinculada a un assumpte particular.
     */
    public function createForAssumpteParticular(AssumpteParticular $peticio, string $documentFirmat): Falta
    {
        $motiu = array_search(
            self::MOTIU_ASSUMPTE_PARTICULAR,
            config('auxiliares.motivoAusencia', []),
            true
        );
        if ($motiu === false) {
            throw new \LogicException('No està configurat el motiu de falta per assumptes particulars.');
        }

        return Falta::query()->create([
            'idProfesor' => $peticio->idProfesor,
            'baja' => 0,
            'dia_completo' => 1,
            'desde' => $peticio->data_gaudi->toDateString(),
            'hasta' => $peticio->data_gaudi->toDateString(),
            'hora_ini' => null,
            'hora_fin' => null,
            'motivos' => (string) $motiu,
            'observaciones' => sprintf('Permís retribuït per assumptes particulars. Sol·licitud #%d.', $peticio->id),
            'fichero' => $documentFirmat,
            'estado' => 3,
        ]);
    }

    /**
     * Actualitza una falta.
     *
     * Quan la falta ja està enviada, només Direcció ha de poder modificar les
     * dades de la comunicació. La resta de fluxos només poden substituir el
     * justificant.
     */
    public function update(int|string $id, Request $request, bool $canEditSubmittedData = false): Falta
    {
        $falta = Falta::findOrFail($id);
        $this->assegurarNoResolucioFirmada($falta);

        if ((int) $falta->estado >= 1 && !$canEditSubmittedData) {
            return $this->updateJustificant($id, $request);
        }

        $diaCompleto = $request->boolean('dia_completo') ? 1 : 0;

        $request->merge([
            'hora_ini' => $diaCompleto ? null : $request->hora_ini,
            'hora_fin' => $diaCompleto ? null : $request->hora_fin,
            'hasta' => esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta,
        ]);

        $falta->fillAll($request);

        $falta = $falta->fresh();
        if ((int) $falta->estado === 1 && !empty($falta->fichero)) {
            (new StateService($falta))->putEstado(2);
            $falta = $falta->fresh();
        }

        return $falta;
    }

    /**
     * Actualitza únicament el justificant d'una falta ja enviada.
     */
    public function updateJustificant(int|string $id, Request $request): Falta
    {
        $falta = Falta::findOrFail($id);
        $this->assegurarNoResolucioFirmada($falta);

        if ($request->hasFile('fichero')) {
            $falta->fichero = $falta->fillFile($request->file('fichero'));
            $falta->save();
            $falta->refresh();
        }

        if ((int) $falta->estado === 1 && !empty($falta->fichero)) {
            (new StateService($falta))->putEstado(2);
            $falta = $falta->fresh();
        }

        return $falta;
    }

    /** Impedeix substituir la resolució d'una petició ja autoritzada. */
    private function assegurarNoResolucioFirmada(Falta $falta): void
    {
        if ($falta->assumpteParticular?->estat === AssumpteParticular::ESTAT_AUTORITZADA) {
            throw ValidationException::withMessages([
                'fichero' => 'La resolució firmada només es pot retirar anul·lant la falta des de Direcció.',
            ]);
        }
    }

    /**
     * Comunica la falta a Direcció i ajusta l'estat inicial.
     */
    public function init(int|string $id): Falta
    {
        $falta = Falta::findOrFail($id);
        if (esMayor($falta->desde, Hoy('Y/m/d'))) {
            app(AdviseTeacher::class)->sendTutorEmail($falta);
        }

        $stSrv = new StateService($falta);
        if ($falta->fichero) {
            $stSrv->putEstado(2);
        } else {
            $stSrv->putEstado(1);
        }

        return $falta->fresh();
    }

    /**
     * Tramita l'alta d'una baixa llarga i reactiva el professor.
     */
    public function alta(int|string $id): Falta
    {
        /** @var Falta $falta */
        $falta = Falta::findOrFail($id);

        DB::transaction(function () use ($falta): void {
            $falta->estado = 3;
            $falta->hasta = new Carbon();
            $falta->baja = 0;
            $falta->save();

            app(TeacherSubstitutionService::class)->reactivate((string) $falta->idProfesor);
        });

        return $falta->fresh();
    }
}
