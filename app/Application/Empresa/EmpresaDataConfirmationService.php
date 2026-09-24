<?php

declare(strict_types=1);

namespace Intranet\Application\Empresa;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Centro;
use Intranet\Entities\Empresa;
use Intranet\Entities\EmpresaDataConfirmation;
use Intranet\Entities\Instructor;
use Intranet\Entities\Profesor;
use Intranet\Mail\EmpresaDataConfirmationMail;
use Intranet\Mail\EmpresaDataConfirmedMail;

/**
 * Gestiona l'emissió i la confirmació pública de dades d'empreses d'FCT.
 */
class EmpresaDataConfirmationService
{
    /**
     * Retorna una única opció per cada empresa assignada al tutor.
     *
     * @return Collection<int, Empresa>
     */
    public function selectableCompaniesForTutor(string $tutorDni): Collection
    {
        $collaborations = Colaboracion::query()
            ->where('tutor', $tutorDni)
            ->with(['Centro.Empresa.dataConfirmations', 'Ciclo'])
            ->orderBy('idCentro')
            ->get();

        return $collaborations
            ->groupBy(fn (Colaboracion $item): int => (int) $item->Centro->idEmpresa)
            ->map(function (Collection $items): Empresa {
                $empresa = $items->first()->Centro->Empresa;
                $empresa->selection_text = $empresa->nombre
                    . ' — ' . $items->pluck('Centro')->unique('id')->count() . ' centre(s)'
                    . ' — ' . $items->count() . ' formació(ns)';
                $empresa->selection_marked = !$empresa->dataConfirmations->contains(
                    fn (EmpresaDataConfirmation $confirmation): bool => $confirmation->sent_at !== null
                );

                return $empresa;
            })
            ->sortBy('nombre')
            ->values();
    }

    /**
     * Genera i envia una sol·licitud per cada empresa seleccionada.
     *
     * @param array<int, int|string> $companyIds
     * @return Collection<int, EmpresaDataConfirmation>
     */
    public function sendForTutor(array $companyIds, Profesor $tutor): Collection
    {
        $selected = $this->selectableCompaniesForTutor((string) $tutor->dni)
            ->whereIn('id', array_map('intval', $companyIds))
            ->values();

        if ($selected->isEmpty()) {
            throw ValidationException::withMessages([
                'empreses' => 'Selecciona almenys una empresa assignada.',
            ]);
        }

        $recipients = $selected->mapWithKeys(function (Empresa $empresa) use ($tutor): array {
            $collaborations = $this->collaborationsForTutorAndCompany((string) $tutor->dni, (int) $empresa->id);
            $recipient = $this->recipientEmail($empresa->email, $collaborations);
            if ($recipient === null) {
                throw ValidationException::withMessages([
                    'email' => "L'empresa {$empresa->nombre} no té cap correu electrònic vàlid.",
                ]);
            }

            return [(int) $empresa->id => $recipient];
        });

        return $selected
            ->map(function (Empresa $empresa) use ($tutor, $recipients): EmpresaDataConfirmation {
                $collaborations = $this->collaborationsForCompany((int) $empresa->id);
                $centerIds = $collaborations
                    ->pluck('idCentro')
                    ->map(fn ($id): int => (int) $id)
                    ->unique()
                    ->values()
                    ->all();
                $recipient = $recipients->get($empresa->id);

                EmpresaDataConfirmation::query()
                    ->where('empresa_id', $empresa->id)
                    ->whereNull('confirmed_at')
                    ->where('expires_at', '>', now())
                    ->update(['expires_at' => now()]);

                $plainToken = Str::random(64);
                $confirmation = EmpresaDataConfirmation::create([
                    'empresa_id' => $empresa->id,
                    'tutor_dni' => $tutor->dni,
                    'recipient_email' => $recipient,
                    'token_hash' => hash('sha256', $plainToken),
                    'colaboracion_ids' => $collaborations->pluck('id')->map(fn ($id): int => (int) $id)->all(),
                    'centro_ids' => $centerIds,
                    'expires_at' => now()->addDays(15),
                ]);
                $confirmation->setRelation('empresa', $empresa);

                $url = route('empresa.confirmacio.show', ['token' => $plainToken]);
                Mail::to($recipient, $empresa->nombre)
                    ->send(new EmpresaDataConfirmationMail(
                        $confirmation,
                        $url,
                        $tutor->fullName,
                        $tutor->email
                    ));
                $confirmation->update(['sent_at' => now()]);

                return $confirmation;
            })
            ->values();
    }

    /**
     * Busca una sol·licitud mitjançant el resum criptogràfic del token.
     */
    public function findByToken(string $plainToken): EmpresaDataConfirmation
    {
        return EmpresaDataConfirmation::query()
            ->where('token_hash', hash('sha256', $plainToken))
            ->with('empresa')
            ->firstOrFail();
    }

    /**
     * Carrega totes les col·laboracions actuals dels centres inclosos en la sol·licitud.
     *
     * @return Collection<int, Colaboracion>
     */
    public function collaborations(EmpresaDataConfirmation $confirmation): Collection
    {
        return Colaboracion::query()
            ->whereIn('idCentro', $confirmation->centro_ids)
            ->whereHas('Centro', fn ($query) => $query->where('idEmpresa', $confirmation->empresa_id))
            ->with(['Centro', 'Ciclo'])
            ->get()
            ->sortBy(fn (Colaboracion $item): string => mb_strtolower(
                ($item->Centro->nombre ?? '') . ' ' . ($item->Ciclo->literal ?? ''),
                'UTF-8'
            ))
            ->values();
    }

    /**
     * Carrega tots els centres capturats en la sol·licitud amb els seus instructors.
     *
     * @return Collection<int, Centro>
     */
    public function centers(EmpresaDataConfirmation $confirmation): Collection
    {
        $centers = Centro::query()
            ->where('idEmpresa', $confirmation->empresa_id)
            ->whereIn('id', $confirmation->centro_ids)
            ->whereHas('colaboraciones')
            ->with('instructores')
            ->orderBy('nombre')
            ->get();
        $centerNames = $centers->pluck('nombre', 'id');

        return $centers->each(function (Centro $center) use ($centers, $centerNames): void {
            $center->instructores->each(function (Instructor $instructor) use ($centers, $centerNames): void {
                $ids = $centers
                    ->filter(fn (Centro $item): bool => $item->instructores->contains('dni', $instructor->dni))
                    ->pluck('id');
                $instructor->confirmation_center_ids = $ids->map(fn ($id): int => (int) $id)->all();
                $instructor->confirmation_center_names = $ids
                    ->map(fn ($id) => $centerNames->get($id))
                    ->values()
                    ->all();
            });
            $center->setRelation('instructores', $center->instructores->sortBy('nombre')->values());
        });
    }

    /**
     * Retorna els instructors una sola vegada i conserva els centres vinculats per a mostrar-los.
     *
     * @return Collection<int, Instructor>
     */
    public function instructors(EmpresaDataConfirmation $confirmation): Collection
    {
        $centers = $this->centers($confirmation);

        return $centers
            ->flatMap(fn (Centro $center) => $center->instructores)
            ->unique('dni')
            ->sortBy('nombre')
            ->values();
    }

    /**
     * Aplica atòmicament les dades confirmades per l'empresa.
     *
     * @param array<string, mixed> $data
     */
    public function confirm(EmpresaDataConfirmation $confirmation, array $data): void
    {
        if (!$confirmation->isActionable()) {
            throw ValidationException::withMessages([
                'token' => 'Este enllaç ja no permet modificar les dades.',
            ]);
        }

        $expected = $this->collaborations($confirmation)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values();
        $confirmed = collect($data['confirmed_colaborations'] ?? [])->map(fn ($id): int => (int) $id)->sort()->values();
        if ($expected->all() !== $confirmed->all()) {
            throw ValidationException::withMessages([
                'confirmed_colaborations' => 'Cal confirmar totes les formacions indicades.',
            ]);
        }

        $centers = $this->centers($confirmation);
        $expectedCenterIds = $centers->pluck('id')->map(fn ($id): int => (int) $id)->sort()->values();
        $receivedCenterIds = collect(array_keys($data['centers'] ?? []))->map(fn ($id): int => (int) $id)->sort()->values();
        if ($expectedCenterIds->all() !== $receivedCenterIds->all() || $centers->count() !== $expectedCenterIds->count()) {
            throw ValidationException::withMessages([
                'centers' => 'Els centres rebuts no corresponen a la sol·licitud.',
            ]);
        }

        $allowedDnis = $this->instructors($confirmation)->pluck('dni')->map(fn ($dni): string => (string) $dni)->sort()->values();
        $receivedDnis = collect(data_get($data, 'instructors.existing', []))
            ->pluck('dni')->map(fn ($dni): string => (string) $dni)->sort()->values();
        if ($allowedDnis->all() !== $receivedDnis->all()) {
            throw ValidationException::withMessages([
                'instructors' => 'Els instructors rebuts no corresponen a la sol·licitud.',
            ]);
        }

        $new = data_get($data, 'instructors.new', []);
        $newDni = strtoupper(trim((string) ($new['dni'] ?? '')));
        $removals = collect($data['instructor_removals'] ?? [])
            ->flatMap(fn (array $dnis, $centerId): Collection => collect($dnis)->map(
                fn ($dni): string => (int) $centerId . ':' . (string) $dni
            ));
        $currentLinks = $centers->flatMap(fn (Centro $center): Collection => $center->instructores->map(
            fn (Instructor $instructor): string => (int) $center->id . ':' . (string) $instructor->dni
        ));
        if ($removals->diff($currentLinks)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'instructor_removals' => 'Les desvinculacions rebudes no corresponen als centres de la sol·licitud.',
            ]);
        }

        $remainingDnis = $currentLinks
            ->diff($removals)
            ->map(fn (string $link): string => explode(':', $link, 2)[1])
            ->unique()
            ->values();
        if ($remainingDnis->isEmpty() && $newDni === '') {
            throw ValidationException::withMessages(['instructors' => 'Cal indicar almenys un instructor.']);
        }

        $coordinatorDni = (string) ($data['coordinator_dni'] ?? '');
        if (!$remainingDnis->contains($coordinatorDni) && !($coordinatorDni === '__new__' && $newDni !== '')) {
            throw ValidationException::withMessages(['coordinator_dni' => 'Cal seleccionar un instructor coordinador.']);
        }

        DB::transaction(function () use (
            $confirmation,
            $data,
            $centers,
            $allowedDnis,
            $new,
            $newDni,
            $coordinatorDni,
            $removals
        ): void {
            $confirmation->empresa->update([
                'nombre' => $data['empresa']['nombre'],
                'cif' => strtoupper(trim($data['empresa']['cif'])),
                'email' => $data['empresa']['email'],
                'telefono' => $data['empresa']['telefono'],
                'direccion' => $data['empresa']['direccion'],
                'localidad' => $data['empresa']['localidad'],
                'gerente' => $data['empresa']['gerente'],
                'nif_gerente' => strtoupper(trim($data['empresa']['nif_gerente'])),
            ]);

            foreach ($centers as $center) {
                $centerData = $data['centers'][$center->id];
                $center->update([
                    'nombre' => $centerData['nombre'],
                    'email' => $centerData['email'] ?? null,
                    'telefono' => $centerData['telefono'] ?? null,
                    'direccion' => $centerData['direccion'],
                    'localidad' => $centerData['localidad'],
                    'horarios' => $centerData['horarios'] ?? null,
                ]);

            }

            foreach (data_get($data, 'instructors.existing', []) as $instructorData) {
                Instructor::whereKey($instructorData['dni'])->update([
                    'name' => $instructorData['name'],
                    'surnames' => $instructorData['surnames'],
                    'email' => $instructorData['email'],
                    'telefono' => $instructorData['telefono'] ?? null,
                ]);
            }

            foreach ($removals as $link) {
                [$centerId, $dni] = explode(':', $link, 2);
                $centers->firstWhere('id', (int) $centerId)?->instructores()->detach($dni);

                $instructor = Instructor::withCount(['Centros', 'Fcts'])->find($dni);
                if ($instructor !== null && $instructor->centros_count === 0 && $instructor->fcts_count === 0) {
                    $instructor->delete();
                }
            }

            if ($newDni !== '') {
                $instructor = Instructor::updateOrCreate(
                    ['dni' => $newDni],
                    [
                        'name' => $new['name'],
                        'surnames' => $new['surnames'],
                        'email' => $new['email'],
                        'telefono' => $new['telefono'] ?? null,
                    ]
                );
                $newCenterIds = collect($new['center_ids'] ?? [])->map(fn ($id): int => (int) $id);
                $centers->whereIn('id', $newCenterIds)->each(
                    fn (Centro $center) => $center->instructores()->syncWithoutDetaching([$instructor->dni])
                );
            }

            $companyInstructorDnis = $allowedDnis->when($newDni !== '', fn (Collection $items) => $items->push($newDni));
            Instructor::query()->whereIn('dni', $companyInstructorDnis)->update(['coordinador' => false]);
            $selectedCoordinator = $coordinatorDni === '__new__' ? $newDni : $coordinatorDni;
            Instructor::query()->whereKey($selectedCoordinator)->update(['coordinador' => true]);

            $confirmation->update(['confirmed_at' => now()]);
        });

        $this->notifyTutor($confirmation);
    }

    /**
     * Avisa el tutor sense anul·lar una confirmació ja guardada si falla el correu.
     */
    private function notifyTutor(EmpresaDataConfirmation $confirmation): void
    {
        try {
            $tutor = $confirmation->tutor()->first();
            if ($tutor === null || filter_var($tutor->email, FILTER_VALIDATE_EMAIL) === false) {
                Log::warning('No s’ha pogut avisar el tutor de la confirmació de dades de l’empresa.', [
                    'confirmation_id' => $confirmation->id,
                    'tutor_dni' => $confirmation->tutor_dni,
                ]);

                return;
            }

            Mail::to($tutor->email, $tutor->fullName)
                ->send(new EmpresaDataConfirmedMail($confirmation->fresh('empresa'), $tutor->fullName));
        } catch (\Throwable $exception) {
            Log::warning('Ha fallat l’avís al tutor després de confirmar les dades de l’empresa.', [
                'confirmation_id' => $confirmation->id,
                'tutor_dni' => $confirmation->tutor_dni,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Resol el primer correu vàlid segons la prioritat empresa, centre i col·laboració.
     */
    private function recipientEmail(?string $companyEmail, Collection $collaborations): ?string
    {
        $candidates = collect([$companyEmail])
            ->concat($collaborations->pluck('Centro.email'))
            ->concat($collaborations->pluck('email'));

        return $candidates->first(fn ($email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * Retorna totes les col·laboracions de l'empresa assignades al tutor.
     *
     * @return Collection<int, Colaboracion>
     */
    private function collaborationsForTutorAndCompany(string $tutorDni, int $companyId): Collection
    {
        return Colaboracion::query()
            ->where('tutor', $tutorDni)
            ->whereHas('Centro', fn ($query) => $query->where('idEmpresa', $companyId))
            ->with(['Centro', 'Ciclo'])
            ->get();
    }

    /**
     * Retorna totes les col·laboracions de tots els centres de l'empresa.
     *
     * @return Collection<int, Colaboracion>
     */
    private function collaborationsForCompany(int $companyId): Collection
    {
        return Colaboracion::query()
            ->whereHas('Centro', fn ($query) => $query->where('idEmpresa', $companyId))
            ->with(['Centro', 'Ciclo'])
            ->get();
    }
}
