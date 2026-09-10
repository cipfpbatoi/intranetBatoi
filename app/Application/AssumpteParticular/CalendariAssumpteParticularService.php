<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\CalendariEscolar;

/**
 * Aplica les restriccions del calendari escolar als assumptes particulars.
 */
class CalendariAssumpteParticularService
{
    private const PATRONS_PERIODE_EXCLOS = ['nadal', 'pasqua', 'festiu local'];

    /**
     * Valida la data i retorna si correspon a un dia lectiu o no lectiu.
     */
    public function validar(
        CarbonInterface $data,
        CarbonInterface $iniciCurs,
        CarbonInterface $fiCurs
    ): string {
        /** @var CalendariEscolar|null $dia */
        $dia = CalendariEscolar::query()->whereDate('data', $data->toDateString())->first();

        if ($dia === null) {
            throw new AssumpteParticularException('La data no està definida en el calendari escolar.');
        }
        if ($dia->tipus === 'festiu') {
            throw new AssumpteParticularException('No es pot demanar el permís en un dia festiu.');
        }
        if ($this->esEsdevenimentBloquejat((string) $dia->esdeveniment)) {
            throw new AssumpteParticularException('La data està bloquejada per un període d’avaluació o examen.');
        }

        $lectius = $this->diesLectius($iniciCurs, $fiCurs);
        $dataSql = $data->toDateString();
        if ($lectius->take(7)->contains($dataSql) || $lectius->take(-7)->contains($dataSql)) {
            throw new AssumpteParticularException('No es pot demanar el permís en els set primers o últims dies lectius.');
        }
        if ($this->estaAlVoltantDunPeriodeExclos($dataSql, $lectius, $iniciCurs, $fiCurs)) {
            throw new AssumpteParticularException('La data està dins dels set dies lectius pròxims a un període exclòs.');
        }

        return $dia->tipus === 'lectiu'
            ? AssumpteParticular::TIPUS_LECTIU
            : AssumpteParticular::TIPUS_NO_LECTIU;
    }

    /**
     * Indica si dues dates són dies lectius consecutius segons el calendari.
     */
    public function sonLectiusConsecutius(
        CarbonInterface $primera,
        CarbonInterface $segona,
        CarbonInterface $iniciCurs,
        CarbonInterface $fiCurs
    ): bool {
        $dies = $this->diesLectius($iniciCurs, $fiCurs)->values();
        $indexPrimera = $dies->search($primera->toDateString(), true);
        $indexSegona = $dies->search($segona->toDateString(), true);

        return $indexPrimera !== false
            && $indexSegona !== false
            && abs((int) $indexPrimera - (int) $indexSegona) === 1;
    }

    /**
     * @return Collection<int, string>
     */
    private function diesLectius(CarbonInterface $inici, CarbonInterface $fi): Collection
    {
        return CalendariEscolar::query()
            ->whereBetween('data', [$inici->toDateString(), $fi->toDateString()])
            ->where('tipus', 'lectiu')
            ->orderBy('data')
            ->pluck('data')
            ->map(static fn ($data): string => CarbonImmutable::parse((string) $data)->toDateString());
    }

    /**
     * Detecta les avaluacions i els exàmens identificats al calendari de Direcció.
     */
    private function esEsdevenimentBloquejat(string $esdeveniment): bool
    {
        $text = mb_strtolower($esdeveniment);

        return $text !== '' && (
            str_contains($text, 'avaluaci')
            || str_contains($text, 'evaluaci')
            || str_contains($text, 'examen')
        );
    }

    /**
     * Comprova els set dies lectius anteriors i posteriors a Nadal, Pasqua i festes locals.
     */
    private function estaAlVoltantDunPeriodeExclos(
        string $data,
        Collection $lectius,
        CarbonInterface $iniciCurs,
        CarbonInterface $fiCurs
    ): bool {
        $esdeveniments = CalendariEscolar::query()
            ->whereBetween('data', [$iniciCurs->toDateString(), $fiCurs->toDateString()])
            ->whereNotNull('esdeveniment')
            ->get(['data', 'esdeveniment']);

        foreach ($esdeveniments as $esdeveniment) {
            $text = mb_strtolower((string) $esdeveniment->esdeveniment);
            if (!collect(self::PATRONS_PERIODE_EXCLOS)->contains(
                static fn (string $patro): bool => str_contains($text, $patro)
            )) {
                continue;
            }

            $diaExclos = CarbonImmutable::parse((string) $esdeveniment->data);
            $anteriors = $lectius->filter(
                static fn (string $dia): bool => CarbonImmutable::parse($dia)->lessThan($diaExclos)
            )->take(-7);
            $posteriors = $lectius->filter(
                static fn (string $dia): bool => CarbonImmutable::parse($dia)->greaterThan($diaExclos)
            )->take(7);

            if ($anteriors->merge($posteriors)->contains($data)) {
                return true;
            }
        }

        return false;
    }
}
