<?php

namespace Intranet\Services;

use Intranet\Entities\Falta_profesor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


/**
 * Servei FitxatgeService.
 */
class FitxatgeService
{
    public function fitxar(string $dni = null):  Falta_profesor|bool|null
    {
        $dni = $dni ?? Auth::user()->dni;

        if (!isPrivateAddress(getClientIpAddress())) {
            return false;
        }

        $ultim = Falta_profesor::Hoy($dni)->get()->last();
        $ara = Carbon::now();

        if ($ultim) {
            $last = $ultim->salida ? new Carbon($ultim->salida) : new Carbon($ultim->entrada);
            if ($ara->diffInMinutes($last) < 10) {
                return null;
            }
        }

        if (!$ultim || $ultim->salida) {
            return $this->fitxaDiaManual($dni, $ara->toDateString(), $ara->toTimeString());
        }

        $ultim->salida = $ara->toTimeString();
        $ultim->save();

        return $ultim;
    }

    public function fitxaDiaManual(string $dni, string $dia, string $hora = '12:00:00'): Falta_profesor
    {
        return Falta_profesor::create([
            'idProfesor' => $dni,
            'dia' => $dia,
            'entrada' => $hora,
        ]);
    }
}

