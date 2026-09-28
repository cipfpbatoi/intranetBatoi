<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Support\Facades\DB;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;
use Intranet\Services\Document\TipoReunionService;

/**
 * Orquestra la creació completa i atòmica d'una reunió.
 */
class CreateReunionService
{
    public function __construct(
        private readonly ReunionCreationGroupResolver $groupResolver,
        private readonly ReunionParticipantAssigner $participantAssigner,
        private readonly ReunionOrderGenerateService $orderGenerator,
        private readonly ReunionFeValuationService $feValuationService
    ) {
    }

    /**
     * Crea la reunió, els participants, els punts i la informació FE.
     */
    public function create(CreateReunionData $data, Profesor $creator): Reunion
    {
        return DB::transaction(function () use ($data, $creator): Reunion {
            $reunion = Reunion::query()->create([
                'tipo' => $data->tipo,
                'grupo' => $data->grupo,
                'idGrupo' => $this->resolveGroup($data, $creator),
                'curso' => $data->curso,
                'numero' => $data->numero,
                'fecha' => $data->fecha,
                'descripcion' => $data->descripcion,
                'objetivos' => $data->objetivos,
                'idProfesor' => (string) $creator->dni,
                'idEspacio' => $data->idEspacio,
            ]);

            $this->participantAssigner->assign($reunion, $creator);
            $this->orderGenerator->generate($reunion);
            $this->feValuationService->ensureOrder($reunion, $reunion->normativa);

            return $reunion;
        });
    }

    /**
     * Normalitza el grup docent exclusivament per a reunions de grup.
     */
    private function resolveGroup(CreateReunionData $data, Profesor $creator): ?string
    {
        $meetingType = new TipoReunionService($data->tipo);
        if ($meetingType->colectivo !== 'Grupo') {
            return null;
        }

        return $this->groupResolver->resolve($creator, $data->idGrupo);
    }
}
