<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Profesor\ProfesorService;

class ProfesorController extends ApiBaseController
{

    protected $model = 'Profesor';
    private ?ProfesorService $profesorService = null;

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    public function rol($dni)
    {
        $profesor = $this->profesores()->find((string) $dni);
        $data = $profesor ? ['rol' => $profesor->rol] : null;
        return $this->sendResponse($data, 'OK');
    }

    public function getRol($rol)
    {
        $all = $this->profesores()->activos();
        $data = [];
        foreach ($all as $profesor) {
            if ($profesor->rol % $rol == 0) {
                $data[$profesor->dni] = $profesor->fullName;
            }
        }
        return $this->sendResponse($data, 'OK');
    }
}
