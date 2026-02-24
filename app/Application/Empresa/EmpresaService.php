<?php

declare(strict_types=1);

namespace Intranet\Application\Empresa;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Intranet\Domain\Empresa\EmpresaRepositoryInterface;
use Intranet\Entities\Centro;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Empresa;
use Intranet\Presentation\Crud\EmpresaCrudSchema;

/**
 * Casos d'ús d'aplicació per al domini d'empreses.
 */
class EmpresaService
{
    public function __construct(private readonly EmpresaRepositoryInterface $empresaRepository)
    {
    }

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function listForGrid(): EloquentCollection
    {
        return $this->empresaRepository->listForGrid();
    }

    public function findForShow(int $empresaId): Empresa
    {
        return $this->empresaRepository->findForShow($empresaId);
    }

    /**
     * @return Collection<int, mixed>
     */
    public function colaboracionIdsForTutorCycle(?int $tutorCycleId, Empresa $empresa): Collection
    {
        if ($tutorCycleId === null) {
            return collect();
        }

        return $this->empresaRepository->colaboracionIdsByCycleAndCenters(
            $tutorCycleId,
            $empresa->centros->pluck('id')->all()
        );
    }

    /**
     * @return EloquentCollection<int, Ciclo>
     */
    public function departmentCycles(?string $department): EloquentCollection
    {
        if ($department === null || $department === '') {
            return collect();
        }

        return $this->empresaRepository->cyclesByDepartment($department);
    }

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function convenioList(): EloquentCollection
    {
        return $this->empresaRepository->convenioList();
    }

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function socialConcertList(): EloquentCollection
    {
        return $this->empresaRepository->socialConcertList();
    }

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function erasmusList(): EloquentCollection
    {
        return $this->empresaRepository->erasmusList();
    }

    /**
     * Persisteix una empresa (alta o edició) amb validació i normalització.
     *
     * @param Request $request
     * @param int|string|null $id
     * @return mixed
     */
    public function saveFromRequest(Request $request, $id = null)
    {
        $request = $this->normalizeRequest($request);
        validator($request->all(), EmpresaCrudSchema::requestRules($id))->validate();

        $elemento = $id ? Empresa::findOrFail($id) : new Empresa();

        return $elemento->fillAll($request);
    }

    /**
     * Crea el centre inicial d'una empresa.
     */
    public function createCenter(int|string $empresaId, Request $request): int
    {
        $centro = new Centro();
        $centro->idEmpresa = $empresaId;
        $centro->direccion = $request->direccion;
        $centro->nombre = $request->nombre;
        $centro->localidad = $request->localidad;
        $centro->save();

        return (int) $centro->id;
    }

    /**
     * Crea col·laboració inicial associada al cicle del tutor.
     */
    public function createColaboration(int|string $centroId, Request $request, int|string $cicloId, string $tutorName): int
    {
        $colaboracion = new Colaboracion();
        $colaboracion->idCentro = $centroId;
        $colaboracion->telefono = $request->telefono;
        $colaboracion->email = $request->email;
        $colaboracion->puestos = 1;
        $colaboracion->tutor = $tutorName;
        $colaboracion->idCiclo = $cicloId;
        $colaboracion->save();

        return (int) $colaboracion->id;
    }

    /**
     * Propaga camps bàsics d'empresa a centres incomplets.
     */
    public function fillMissingCenterData(Empresa $empresa): void
    {
        foreach ($empresa->centros as $centro) {
            $touched = false;

            if ($centro->direccion == '') {
                $centro->direccion = $empresa->direccion;
                $touched = true;
            }
            if ($centro->localidad == '') {
                $centro->localidad = $empresa->localidad;
                $touched = true;
            }
            if ($centro->nombre == '') {
                $centro->nombre = $empresa->nombre;
                $touched = true;
            }

            if ($touched) {
                $centro->save();
            }
        }
    }

    /**
     * Normalitza checkbox i CIF en l'entrada del formulari d'empresa.
     */
    private function normalizeRequest(Request $request): Request
    {
        $checkboxes = ['europa', 'sao', 'dual', 'delitos', 'menores'];
        $normalized = [];

        foreach ($checkboxes as $field) {
            $normalized[$field] = $request->boolean($field);
        }

        if ($request->filled('cif')) {
            $normalized['cif'] = strtoupper((string) $request->input('cif'));
        }

        $request->merge($normalized);

        return $request;
    }
}
