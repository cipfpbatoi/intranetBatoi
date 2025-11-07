<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PresenciaResumenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresenciaResumenController extends Controller
{
    public function dia(Request $request, PresenciaResumenService $svc)
    {
        $dia = $request->query('dia', Carbon::today()->toDateString());
        $dep = $request->query('departamento');

        $profes = DB::table('profesores')
            ->select('dni','nombre','apellido1','apellido2','departamento')
            ->where('activo', 1)
            ->when($dep, fn($q)=>$q->where('departamento',$dep))
            ->orderBy('departamento')->orderBy('apellido1')->orderBy('apellido2')
            ->get();

        return response()->json($svc->resumenDia($dia, $profes));
    }

    public function rango(Request $request, PresenciaResumenService $svc)
    {
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');
        if (!$desde || !$hasta) return response()->json(['error'=>'Missing desde/hasta'], 422);

        $out = [];
        $cursor = Carbon::parse($desde)->startOfDay();
        $end    = Carbon::parse($hasta)->startOfDay();
        while ($cursor->lte($end)) {
            $out[$cursor->toDateString()] = $svc->resumenDia($cursor->toDateString());
            $cursor->addDay();
        }
        return response()->json($out);
    }
}