<?php

namespace Intranet\Http\Controllers\Api;

use Intranet\Services\HR\PresenciaResumenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Intranet\Http\Controllers\API\ApiResourceController;

class PresenciaResumenController extends ApiResourceController
{
    public function rango(Request $request, PresenciaResumenService $svc)
    {
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');
        $dni   = $request->query('dni'); // <- filtre opcional per professor

        if (!$desde || !$hasta) {
            return $this->sendError('Missing desde/hasta', 422);
        }

        $desdeDate = Carbon::parse($desde)->startOfDay();
        $hastaDate = Carbon::parse($hasta)->startOfDay();
        if ($hastaDate->lt($desdeDate)) {
            [$desdeDate, $hastaDate] = [$hastaDate, $desdeDate]; // intercanvia si venen al revés
        }

        // Professors actius, opcionalment filtrats per DNI
        $profes = DB::table('profesores')
            ->select('dni','nombre','apellido1','apellido2','departamento')
            ->where('activo', 1)
            ->when($dni, fn($q) => $q->where('dni', $dni))
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->orderBy('nombre')
            ->get();

        // Si no hi ha profes (per exemple, DNI inexistent), retornem array buit
        if ($profes->isEmpty()) {
            return response()->json([]);
        }

        // Preparem estructura base per professor
        $result = [];
        foreach ($profes as $p) {
            $result[$p->dni] = [
                'dni'         => $p->dni,
                'nombre'      => $p->nombre,
                'apellido1'   => $p->apellido1,
                'apellido2'   => $p->apellido2,
                'departamento'=> $p->departamento,
                'days'        => [] // es farceix per data
            ];
        }

        // Recorrem els dies del rang i reutilitzem el servei
        $cursor = $desdeDate->copy();
        while ($cursor->lte($hastaDate)) {
            $diaStr = $cursor->toDateString();

            $resumenDia = $svc->resumenDia($diaStr, $profes); // array per profe eixe dia

            foreach ($resumenDia as $row) {
                $dniRow = $row['dni'];
                if (!isset($result[$dniRow])) continue;
                $result[$dniRow]['days'][$diaStr] = [
                    'status'                     => $row['status'],
                    'planned_docencia_minutes'   => $row['planned_docencia_minutes'],
                    'planned_altres_minutes'     => $row['planned_altres_minutes'],
                    'covered_docencia_minutes'   => $row['covered_docencia_minutes'],
                    'covered_altres_minutes'     => $row['covered_altres_minutes'],
                    'in_center_minutes'          => $row['in_center_minutes'],
                    'has_open_stay'              => $row['has_open_stay'] ?? false,
                    'first_entry'                => $row['first_entry'] ?? null,
                ];
            }

            $cursor->addDay();
        }

        return response()->json(array_values($result));
    }
}
