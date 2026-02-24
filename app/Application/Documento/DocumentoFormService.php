<?php

declare(strict_types=1);

namespace Intranet\Application\Documento;

use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Entities\AlumnoFct;

class DocumentoFormService
{
    public function updateNota(AlumnoFctService $alumnoFctService, int|string $fctId, mixed $nota): void
    {
        $fctAl = $alumnoFctService->findOrFail($fctId);
        $fctAl->calProyecto = $nota;
        if ($fctAl->calificacion < 1) {
            $fctAl->calificacion = 1;
        }
        $fctAl->save();
    }

    public function projectDefaults(AlumnoFct $fct, string $ciclo, string $supervisor): array
    {
        $proyecto = $fct->Alumno->Projecte ?? null;

        return [
            'curso' => Curso(),
            'propietario' => $fct->Alumno->FullName,
            'supervisor' => $supervisor,
            'activo' => true,
            'tipoDocumento' => 'Proyecto',
            'idDocumento' => '',
            'ciclo' => $ciclo,
            'descripcion' => $proyecto->titol ?? '',
            'detalle' => $proyecto->descripcio ?? '',
        ];
    }

    public function qualitatDefaults(mixed $grupo, string $fullName): array
    {
        return [
            'curso' => Curso(),
            'propietario' => $fullName,
            'supervisor' => $fullName,
            'activo' => true,
            'tipoDocumento' => 'FCT',
            'idDocumento' => '',
            'ciclo' => $grupo->Ciclo->ciclo,
            'grupo' => $grupo->nombre,
            'tags' => 'Fct,Entrevista,Alumnat,Instructor',
            'instrucciones' => 'Pujar en un sols document comprimit: Entrevista Alumnat i Entrevista Instructor',
        ];
    }
}
