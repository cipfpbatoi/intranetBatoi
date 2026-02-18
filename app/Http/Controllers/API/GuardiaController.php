<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Guardia;

class GuardiaController extends ApiBaseController
{
    protected $model = 'Guardia';

    public function show($cadena, $send = true)
    {
        $cadena = (string) $cadena;

        // Pont legacy per al format actual del frontend:
        // /api/guardia/dia]YYYY-MM-DD&dia[YYYY-MM-DD
        if (preg_match('/^dia\]([^&]+)&dia\[([^&]+)$/', $cadena, $matches) === 1) {
            $data = $this->queryByDiaRange((string) $matches[1], (string) $matches[2]);
            return $send ? $this->sendResponse($data, 'OK') : $data;
        }

        $data = parent::show($cadena, false);
        return $send ? $this->sendResponse($data, 'OK') : $data;
    }

    public function range(Request $request)
    {
        $desde = (string) ($request->query('desde', $request->input('desde')) ?? '');
        $hasta = (string) ($request->query('hasta', $request->input('hasta')) ?? '');

        if ($desde === '' || $hasta === '') {
            return $this->sendFail(['success' => false, 'message' => 'Falten paràmetres: desde i hasta'], 422);
        }

        return $this->sendResponse($this->queryByDiaRange($desde, $hasta), 'OK');
    }

    public function getServerTime()
    {
        return response()->json([
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ]);
    }

    private function queryByDiaRange(string $desde, string $hasta)
    {
        return Guardia::query()
            ->whereBetween('dia', [$desde, $hasta])
            ->get();
    }
}

