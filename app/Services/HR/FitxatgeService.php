<?php

namespace Intranet\Services\HR;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\FaltaProfesor\FaltaProfesorRepositoryInterface;
use Intranet\Entities\Falta_profesor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class FitxatgeService
{
    public function __construct(private readonly FaltaProfesorRepositoryInterface $faltaProfesorRepository)
    {
    }

    public function fitxar(?string $dni = null):  Falta_profesor|bool|null
    {
        $dni = $dni ?? Auth::user()->dni;

        if (!isPrivateAddress(getClientIpAddress())) {
            return false;
        }

        $ultim = $this->faltaProfesorRepository->lastTodayByProfesor($dni);
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

        return $this->faltaProfesorRepository->closeExit($ultim, $ara->toTimeString());
    }

    public function fitxaDiaManual(string $dni, string $dia, string $hora = '12:00:00'): Falta_profesor
    {
        return $this->faltaProfesorRepository->createEntry($dni, $dia, $hora);
    }

    public function hasFichado(string $dia, string $dni): bool
    {
        return $this->faltaProfesorRepository->hasFichadoOnDay($dia, $dni);
    }

    public function isInside(?string $dni = null, bool $storeInSession = true): bool
    {
        $dni = $dni ?? Auth::user()->dni;
        $ultimo = $this->faltaProfesorRepository->lastTodayByProfesor($dni);

        if ($storeInSession) {
            session(['ultimoFichaje' => $ultimo]);
        }

        return $ultimo !== null && $ultimo->salida === null;
    }

    public function sessionEntry(): ?string
    {
        $registro = session('ultimoFichaje');
        if (!$registro && Auth::check()) {
            $this->isInside(Auth::user()->dni, true);
            $registro = session('ultimoFichaje');
        }

        if (isset($registro->entrada)) {
            return substr((string) $registro->entrada, 0, 5);
        }

        return null;
    }

    public function sessionExit(): ?string
    {
        $registro = session('ultimoFichaje');
        if (!$registro && Auth::check()) {
            $this->isInside(Auth::user()->dni, true);
            $registro = session('ultimoFichaje');
        }

        if (isset($registro->salida)) {
            return substr((string) $registro->salida, 0, 5);
        }

        return null;
    }

    public function wasInsideAt(string $dni, string $dia, string $hora): bool
    {
        $fichadas = $this->faltaProfesorRepository->byDayAndProfesor($dia, $dni);

        foreach ($fichadas as $ficha) {
            if ($ficha->salida && $hora >= $ficha->entrada && $hora < $ficha->salida) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return EloquentCollection<int, Falta_profesor>
     */
    public function registrosEntreFechas(string $dni, string $desde, string $hasta): EloquentCollection
    {
        return $this->faltaProfesorRepository->rangeByProfesor($dni, $desde, $hasta);
    }
}
