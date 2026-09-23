<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;
use Intranet\Services\Document\TipoReunionService;

/**
 * Manté la continuïtat dels punts entre actes successives d'un grup.
 */
class ReunionContinuityService
{
    public const DEFAULT_SUMMARY = 'No procedeix';

    private const PREVIOUS_AGREEMENTS = 'Acords adoptats';

    private const AGREEMENTS_REVIEW = "Revisió d'acords adoptats a la sessió anterior";

    private const NESE_FOLLOW_UP = 'Alumnes amb dificultats acadèmiques i mesures a adoptar';

    /**
     * Recupera els resums que la nova acta ha d'heretar de l'anterior.
     *
     * @return array<string, string>
     */
    public function inheritedSummaries(Reunion $reunion): array
    {
        $previous = $this->previousArchivedAct($reunion);
        if ($previous === null) {
            return [];
        }

        $summaries = $previous->ordenes()
            ->whereIn('descripcion', [self::PREVIOUS_AGREEMENTS, self::NESE_FOLLOW_UP])
            ->pluck('resumen', 'descripcion');

        $inherited = [];
        $agreements = $summaries->get(self::PREVIOUS_AGREEMENTS);
        if ($this->hasMeaningfulContent($agreements)) {
            $inherited[self::AGREEMENTS_REVIEW] = (string) $agreements;
        }

        $nese = $summaries->get(self::NESE_FOLLOW_UP);
        if ($this->hasMeaningfulContent($nese)) {
            $inherited[self::NESE_FOLLOW_UP] = (string) $nese;
        }

        return $inherited;
    }

    /**
     * Substituïx els resums sense contingut real abans d'arxivar l'acta.
     */
    public function normaliseEmptySummaries(Reunion $reunion): void
    {
        $reunion->ordenes()->get()->each(function (OrdenReunion $order): void {
            $normalised = $this->normaliseSummary($order->resumen);
            if ($order->resumen !== $normalised) {
                $order->resumen = $normalised;
                $order->save();
            }
        });
    }

    /**
     * Retorna el resum original o el text auditable per defecte si està buit.
     */
    public function normaliseSummary(mixed $summary): string
    {
        return $this->hasMeaningfulContent($summary)
            ? (string) $summary
            : self::DEFAULT_SUMMARY;
    }

    /**
     * Localitza l'última acta arxivada anterior del mateix grup i curs.
     */
    private function previousArchivedAct(Reunion $reunion): ?Reunion
    {
        if ($reunion->Tipos()->colectivo !== 'Grupo') {
            return null;
        }

        $date = $reunion->getRawOriginal('fecha') ?: $reunion->fecha;
        $group = $reunion->grupoDocenteActual();
        $groupCode = $reunion->idGrupo ?: $group?->codigo;
        $legacyTutor = $group?->tutor ?: $reunion->idProfesor;

        $query = Reunion::query()
            ->where('id', '!=', $reunion->getKey())
            ->whereIn('tipo', $this->groupMeetingTypes())
            ->where('curso', $reunion->curso)
            ->where('archivada', 1)
            ->where('fecha', '<', $date);

        if ($groupCode) {
            $query->where(function ($groupQuery) use ($groupCode, $legacyTutor): void {
                $groupQuery->where('idGrupo', $groupCode)
                    ->orWhere(function ($legacyQuery) use ($legacyTutor): void {
                        $legacyQuery->whereNull('idGrupo')
                            ->where('idProfesor', $legacyTutor);
                    });
            });
        } else {
            $query->whereNull('idGrupo')->where('idProfesor', $legacyTutor);
        }

        return $query->orderByDesc('fecha')->orderByDesc('id')->first();
    }

    /**
     * Retorna només els tipus vinculats a un grup docent.
     *
     * @return array<int, int>
     */
    private function groupMeetingTypes(): array
    {
        return collect(TipoReunionService::all())
            ->filter(static fn (array $type): bool => ($type['colectivo'] ?? null) === 'Grupo')
            ->pluck('index')
            ->map(static fn (mixed $type): int => (int) $type)
            ->values()
            ->all();
    }

    /**
     * Detecta text real i descarta espais, entitats i HTML buit.
     */
    private function hasMeaningfulContent(mixed $summary): bool
    {
        if ($summary === null) {
            return false;
        }

        $decoded = html_entity_decode((string) $summary, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainText = strip_tags(str_replace("\u{00A0}", ' ', $decoded));

        return preg_replace('/\s+/u', '', $plainText) !== '';
    }
}
