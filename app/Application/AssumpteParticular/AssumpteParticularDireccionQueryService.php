<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Intranet\Entities\AssumpteParticular;

/**
 * Prepara el panell diari de peticions pendents per a Direcció.
 */
class AssumpteParticularDireccionQueryService
{
    public function __construct(
        private readonly AssumpteParticularService $assumpteService,
        private readonly CalendariAssumpteParticularService $calendariService,
        private readonly ContingentAssumpteParticularCalculator $contingentCalculator,
        private readonly TornAssumpteParticularService $tornService
    ) {
    }

    /**
     * Agrupa les peticions pendents per data amb prioritat, quotes i avisos.
     *
     * @return array<int, array<string, mixed>>
     */
    public function grupsPendents(?string $data = null): array
    {
        $peticions = AssumpteParticular::query()
            ->with('profesor')
            ->where('estat', AssumpteParticular::ESTAT_PENDENT)
            ->when(filled($data), static fn ($query) => $query->whereDate('data_gaudi', $data))
            ->orderBy('data_gaudi')
            ->get();

        if ($peticions->isEmpty()) {
            return [];
        }

        $quotes = $this->contingentCalculator->quotes($this->tornService->plantillaPerTorn());
        $dates = $peticions
            ->pluck('data_gaudi')
            ->map(static fn ($data): string => CarbonImmutable::parse((string) $data)->toDateString())
            ->unique()
            ->values();
        $autoritzades = AssumpteParticular::query()
            ->where('estat', AssumpteParticular::ESTAT_AUTORITZADA)
            ->where(function ($query) use ($dates): void {
                foreach ($dates as $data) {
                    $query->orWhereDate('data_gaudi', $data);
                }
            })
            ->get(['data_gaudi', 'torn']);
        $diesGaudits = $this->diesGaudits($peticions);

        return $peticions
            ->groupBy(static fn (AssumpteParticular $peticio): string => $peticio->data_gaudi->toDateString())
            ->map(function (Collection $peticionsDia, string $data) use (
                $quotes,
                $autoritzades,
                $diesGaudits
            ): array {
                $autoritzadesDia = $autoritzades->filter(
                    static fn (AssumpteParticular $peticio): bool => $peticio->data_gaudi->toDateString() === $data
                );
                $resumQuotes = $this->resumQuotes($quotes, $autoritzadesDia);
                $sensePlacesGlobals = $autoritzadesDia->count() >= array_sum($quotes);
                $files = $peticionsDia
                    ->map(fn (AssumpteParticular $peticio): array => $this->presentarPeticio(
                        $peticio,
                        $diesGaudits,
                        $resumQuotes,
                        $sensePlacesGlobals
                    ))
                    ->values()
                    ->all();

                usort($files, static function (array $esquerra, array $dreta): int {
                    return $esquerra['dies_gaudits'] <=> $dreta['dies_gaudits']
                        ?: $esquerra['hores_lectives'] <=> $dreta['hores_lectives']
                        ?: $esquerra['ordre_sollicitud'] <=> $dreta['ordre_sollicitud']
                        ?: $esquerra['id'] <=> $dreta['id'];
                });

                return [
                    'data' => $data,
                    'data_formatada' => CarbonImmutable::parse($data)->format('d/m/Y'),
                    'quota_total' => array_sum($quotes),
                    'autoritzades_total' => $autoritzadesDia->count(),
                    'disponibles_total' => max(0, array_sum($quotes) - $autoritzadesDia->count()),
                    'quotes' => $resumQuotes,
                    'peticions' => $files,
                ];
            })
            ->sortBy('data')
            ->values()
            ->all();
    }

    /**
     * Retorna totes les autoritzacions del curs per a l'històric de Direcció.
     *
     * @return array<int, array<string, mixed>>
     */
    public function autoritzades(string $curs): array
    {
        return AssumpteParticular::query()
            ->with(['profesor', 'resolutor'])
            ->where('curs', $curs)
            ->where('estat', AssumpteParticular::ESTAT_AUTORITZADA)
            ->orderByDesc('data_gaudi')
            ->orderByDesc('id')
            ->get()
            ->map(static fn (AssumpteParticular $peticio): array => [
                'id' => (int) $peticio->id,
                'professor' => $peticio->profesor?->fullName ?? $peticio->idProfesor,
                'dni' => $peticio->idProfesor,
                'data_formatada' => $peticio->data_gaudi->format('d/m/Y'),
                'tipus' => $peticio->tipus,
                'origen' => $peticio->origen,
                'resolta_at' => $peticio->resolta_at?->format('d/m/Y H:i') ?? '—',
                'resolta_per' => $peticio->resolutor?->fullName ?? $peticio->resolta_per ?? '—',
                'te_document' => filled($peticio->resolucio_document),
            ])
            ->all();
    }

    /**
     * @param Collection<int, AssumpteParticular> $peticions
     * @return array<string, int>
     */
    private function diesGaudits(Collection $peticions): array
    {
        $professors = $peticions->pluck('idProfesor')->unique()->values();
        $cursos = $peticions->pluck('curs')->unique()->values();

        return AssumpteParticular::query()
            ->selectRaw('idProfesor, curs, COUNT(*) as total')
            ->where('estat', AssumpteParticular::ESTAT_AUTORITZADA)
            ->whereIn('idProfesor', $professors)
            ->whereIn('curs', $cursos)
            ->groupBy('idProfesor', 'curs')
            ->get()
            ->mapWithKeys(static fn (AssumpteParticular $peticio): array => [
                $peticio->idProfesor . '|' . $peticio->curs => (int) $peticio->getAttribute('total'),
            ])
            ->all();
    }

    /**
     * @param array<string, int> $quotes
     * @param Collection<int, AssumpteParticular> $autoritzades
     * @return array<string, array{quota: int, autoritzades: int, disponibles: int}>
     */
    private function resumQuotes(array $quotes, Collection $autoritzades): array
    {
        $resum = [];
        foreach ($quotes as $torn => $quota) {
            $ocupades = $autoritzades->where('torn', $torn)->count();
            $resum[$torn] = [
                'quota' => $quota,
                'autoritzades' => $ocupades,
                'disponibles' => max(0, $quota - $ocupades),
            ];
        }

        return $resum;
    }

    /**
     * @param array<string, int> $diesGaudits
     * @param array<string, array{quota: int, autoritzades: int, disponibles: int}> $quotes
     * @return array<string, mixed>
     */
    private function presentarPeticio(
        AssumpteParticular $peticio,
        array $diesGaudits,
        array $quotes,
        bool $sensePlacesGlobals
    ): array {
        $tornActual = $this->tornService->delProfessor($peticio->idProfesor);
        $avisos = $this->avisos($peticio, $tornActual, $quotes, $sensePlacesGlobals);
        $sollicitadaAt = $peticio->sollicitada_at ?? $peticio->created_at;

        return [
            'id' => (int) $peticio->id,
            'dni' => $peticio->idProfesor,
            'professor' => $peticio->profesor?->fullName ?? $peticio->idProfesor,
            'tipus' => $peticio->tipus,
            'torn' => $peticio->torn,
            'torn_actual' => $tornActual,
            'estat' => $peticio->estat,
            'dies_gaudits' => $diesGaudits[$peticio->idProfesor . '|' . $peticio->curs] ?? 0,
            'hores_lectives' => $this->tornService->horesLectivesAfectades(
                $peticio->idProfesor,
                $peticio->data_gaudi
            ),
            'motivacio_excepcional' => $peticio->motivacio_excepcional,
            'excepcional' => filled($peticio->motivacio_excepcional),
            'avisos' => $avisos,
            'sollicitada_at' => $sollicitadaAt?->format('d/m/Y H:i') ?? '—',
            'ordre_sollicitud' => $sollicitadaAt?->getTimestamp() ?? PHP_INT_MAX,
        ];
    }

    /**
     * @param array<string, array{quota: int, autoritzades: int, disponibles: int}> $quotes
     * @return array<int, string>
     */
    private function avisos(
        AssumpteParticular $peticio,
        string $tornActual,
        array $quotes,
        bool $sensePlacesGlobals
    ): array
    {
        $avisos = [];

        try {
            [$inici, $fi] = $this->limitsDelCurs($peticio->curs);
            $tipusActual = $this->calendariService->validar($peticio->data_gaudi, $inici, $fi);
            if ($tipusActual !== $peticio->tipus) {
                $avisos[] = 'El tipus del dia ha canviat en el calendari escolar.';
            }
        } catch (AssumpteParticularException $exception) {
            $avisos[] = $exception->getMessage();
        }

        if ($tornActual !== $peticio->torn) {
            $avisos[] = 'El torn docent actual no coincidix amb el registrat en la petició.';
        }
        if ($this->assumpteService->saldo($peticio->idProfesor, $peticio->curs, $peticio->tipus) < 1.0) {
            $avisos[] = 'El professor ja no disposa d’un dia complet d’este tipus.';
        }
        if (($quotes[$peticio->torn]['disponibles'] ?? 0) === 0) {
            $avisos[] = 'No queden places disponibles per al torn registrat.';
        }
        if ($sensePlacesGlobals) {
            $avisos[] = 'No queden places disponibles en el contingent global del dia.';
        }

        return array_values(array_unique($avisos));
    }

    /**
     * @return array{CarbonImmutable, CarbonImmutable}
     */
    private function limitsDelCurs(string $curs): array
    {
        if (!preg_match('/^(\d{4})-(\d{4})$/', $curs, $parts) || (int) $parts[2] !== (int) $parts[1] + 1) {
            throw new AssumpteParticularException('El curs indicat no és vàlid.');
        }

        return [
            CarbonImmutable::create((int) $parts[1], 9, 1)->startOfDay(),
            CarbonImmutable::create((int) $parts[2], 7, 31)->startOfDay(),
        ];
    }
}
