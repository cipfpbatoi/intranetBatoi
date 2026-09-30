<?php

declare(strict_types=1);

namespace Intranet\Application\Tutoria;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Grupo;
use Intranet\Entities\Tutoria;
use Intranet\Entities\TutoriaFeedbackNotification;
use Intranet\Entities\TutoriaGrupo;
use Intranet\Services\Notifications\NotificationService;

/**
 * Calcula el seguiment del feedback de tutories i notifica els pendents.
 */
class TutoriaFeedbackService
{
    /**
     * Crea el servei amb el canal intern de notificacions.
     */
    public function __construct(private ?NotificationService $notificationService = null)
    {
        $this->notificationService = $notificationService ?? app(NotificationService::class);
    }

    /**
     * Afig a cada tutoria el resum de progrés que mostrarà Orientació.
     *
     * @param EloquentCollection<int, Tutoria> $tutories
     */
    public function attachProgress(EloquentCollection $tutories): void
    {
        if ($tutories->isEmpty()) {
            return;
        }

        $groups = Grupo::query()->with('Ciclo')->get();
        $feedbackByTutoria = TutoriaGrupo::query()
            ->whereIn('idTutoria', $tutories->modelKeys())
            ->get()
            ->groupBy('idTutoria');

        foreach ($tutories as $tutoria) {
            $progress = $this->progressFromCollections(
                $tutoria,
                $groups,
                $feedbackByTutoria->get($tutoria->getKey(), collect())
            );

            $tutoria->setAttribute(
                'feedbackProgress',
                sprintf('%d / %d (%d%%)', $progress['completed'], $progress['total'], $progress['percentage'])
            );
        }
    }

    /**
     * Calcula totals i percentatge d'una tutoria.
     *
     * @return array{completed: int, total: int, percentage: int}
     */
    public function progress(Tutoria $tutoria): array
    {
        return $this->progressFromCollections(
            $tutoria,
            Grupo::query()->with('Ciclo')->get(),
            TutoriaGrupo::query()->where('idTutoria', $tutoria->getKey())->get()
        );
    }

    /**
     * Envia una única notificació per cada tutoria i grup pendents.
     */
    public function notifyPending(DateTimeInterface $today): int
    {
        $tutories = Tutoria::query()
            ->whereDate('hasta', '<', $today->format('Y-m-d'))
            ->get();

        if ($tutories->isEmpty()) {
            return 0;
        }

        $groups = Grupo::query()->with(['Ciclo', 'Tutor'])->get();
        $feedbackByTutoria = TutoriaGrupo::query()
            ->whereIn('idTutoria', $tutories->modelKeys())
            ->get()
            ->groupBy('idTutoria');
        $sent = 0;

        foreach ($tutories as $tutoria) {
            $feedback = $feedbackByTutoria->get($tutoria->getKey(), collect());
            $completedGroups = $this->completedGroupCodes($feedback);

            foreach ($this->applicableGroups($tutoria, $groups) as $group) {
                if ($completedGroups->contains((string) $group->codigo)) {
                    continue;
                }

                $tutor = $group->Tutor;
                if (!$tutor || trim((string) $tutor->dni) === '') {
                    continue;
                }

                $notified = DB::transaction(function () use ($tutoria, $group, $tutor): bool {
                    $created = TutoriaFeedbackNotification::query()->insertOrIgnore([
                        'idTutoria' => $tutoria->getKey(),
                        'idGrupo' => $group->codigo,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($created === 0) {
                        return false;
                    }

                    $message = "Tens pendent el feedback de la tutoria «{$tutoria->descripcion}» "
                        . "per al grup {$group->nombre}.";
                    $link = route('tutoria.anexo', ['menu' => $tutoria->getKey()], false);
                    $this->notificationService->send((string) $tutor->dni, $message, $link, 'Sistema');

                    return true;
                });

                if ($notified) {
                    $sent++;
                }
            }
        }

        return $sent;
    }

    /**
     * Determina si les observacions contenen text visible.
     */
    public function hasMeaningfulFeedback(?string $observations): bool
    {
        $plainText = html_entity_decode(strip_tags((string) $observations), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainText = preg_replace('/[\s\x{00A0}\x{200B}\x{FEFF}]+/u', '', $plainText) ?? '';

        return $plainText !== '';
    }

    /**
     * @param EloquentCollection<int, Grupo> $groups
     * @param iterable<int, TutoriaGrupo> $feedback
     * @return array{completed: int, total: int, percentage: int}
     */
    private function progressFromCollections(Tutoria $tutoria, EloquentCollection $groups, iterable $feedback): array
    {
        $applicableCodes = $this->applicableGroups($tutoria, $groups)
            ->pluck('codigo')
            ->map(static fn ($code): string => (string) $code);
        $completed = $this->completedGroupCodes($feedback)
            ->intersect($applicableCodes)
            ->count();
        $total = $applicableCodes->count();

        return [
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total === 0 ? 0 : (int) round(($completed / $total) * 100),
        ];
    }

    /**
     * Aplica la selecció de grups configurada en la tutoria.
     *
     * @param EloquentCollection<int, Grupo> $groups
     * @return EloquentCollection<int, Grupo>
     */
    private function applicableGroups(Tutoria $tutoria, EloquentCollection $groups): EloquentCollection
    {
        $type = (int) $tutoria->grupos;
        if ($type === 0) {
            return $groups;
        }

        return $groups
            ->filter(static fn (Grupo $group): bool => (int) ($group->Ciclo?->tipo ?? 0) === $type)
            ->values();
    }

    /**
     * @param iterable<int, TutoriaGrupo> $feedback
     * @return Collection<int, string>
     */
    private function completedGroupCodes(iterable $feedback): Collection
    {
        return collect($feedback)
            ->filter(fn (TutoriaGrupo $item): bool => $this->hasMeaningfulFeedback($item->observaciones))
            ->pluck('idGrupo')
            ->map(static fn ($code): string => (string) $code)
            ->unique()
            ->values();
    }
}
