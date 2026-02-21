<?php

declare(strict_types=1);

namespace Intranet\Application\Falta;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Falta;
use Intranet\Services\General\StateService;
use Intranet\Services\Notifications\AdviseTeacher;
use Intranet\Services\School\TeacherSubstitutionService;
use Jenssegers\Date\Date;

/**
 * Casos d'ús d'aplicació per al domini de faltes de professorat.
 */
class FaltaService
{
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

    public function update(int|string $id, Request $request): Falta
    {
        $diaCompleto = $request->boolean('dia_completo') ? 1 : 0;

        $request->merge([
            'hora_ini' => $diaCompleto ? null : $request->hora_ini,
            'hora_fin' => $diaCompleto ? null : $request->hora_fin,
            'hasta' => esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta,
        ]);

        $falta = Falta::findOrFail($id);
        $falta->fillAll($request);

        $falta = $falta->fresh();
        if ((int) $falta->estado === 1 && !empty($falta->fichero)) {
            (new StateService($falta))->putEstado(2);
            $falta = $falta->fresh();
        }

        return $falta;
    }

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

    public function alta(int|string $id): Falta
    {
        /** @var Falta $falta */
        $falta = Falta::findOrFail($id);

        DB::transaction(function () use ($falta): void {
            $falta->estado = 3;
            $falta->hasta = new Date();
            $falta->baja = 0;
            $falta->save();

            app(TeacherSubstitutionService::class)->reactivate((string) $falta->idProfesor);
        });

        return $falta->fresh();
    }
}
