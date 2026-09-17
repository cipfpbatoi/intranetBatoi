<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intranet\Application\Falta\FaltaService;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Profesor;
use Throwable;

/**
 * Casos d'ús del cicle de vida dels assumptes particulars.
 */
class AssumpteParticularService
{
    public function __construct(
        private readonly SaldoAssumpteParticularCalculator $saldoCalculator,
        private readonly ContingentAssumpteParticularCalculator $contingentCalculator,
        private readonly CalendariAssumpteParticularService $calendariService,
        private readonly TornAssumpteParticularService $tornService,
        private readonly FaltaService $faltaService,
        private readonly RubricaAssumpteParticularService $rubriques
    ) {
    }

    /**
     * Crea una petició pendent sense generar cap falta.
     */
    public function crear(
        string $dni,
        CarbonInterface|string $dataGaudi,
        ?string $motivacioExcepcional = null,
        ?string $plaActivitats = null,
        CarbonInterface|string|null $hui = null
    ): AssumpteParticular {
        $previsualitzacio = $this->previsualitzar(
            $dni,
            $dataGaudi,
            $motivacioExcepcional,
            $plaActivitats,
            $hui
        );
        $this->rubriques->path(Profesor::query()->findOrFail($dni));

        return AssumpteParticular::query()->create([
            'idProfesor' => $dni,
            'data_gaudi' => $previsualitzacio['data'],
            'curs' => $previsualitzacio['curs'],
            'tipus' => $previsualitzacio['tipus'],
            'torn' => $previsualitzacio['torn'],
            'estat' => AssumpteParticular::ESTAT_PENDENT,
            'motivacio_excepcional' => filled($motivacioExcepcional) ? trim($motivacioExcepcional) : null,
            'pla_activitats' => filled($plaActivitats) ? trim($plaActivitats) : null,
            'sollicitada_at' => now(),
        ]);
    }

    /**
     * Valida una proposta sense persistir-la i retorna les dades derivades.
     *
     * @return array{data: string, curs: string, tipus: string, torn: string, saldo: float, excepcional: bool}
     */
    public function previsualitzar(
        string $dni,
        CarbonInterface|string $dataGaudi,
        ?string $motivacioExcepcional = null,
        ?string $plaActivitats = null,
        CarbonInterface|string|null $hui = null
    ): array {
        $profesor = Profesor::query()->findOrFail($dni);
        $data = $this->data($dataGaudi);
        $avui = $hui === null ? CarbonImmutable::today() : $this->data($hui);
        [$curs, $iniciCurs, $fiCurs] = $this->cursDe($data);

        $this->validarTermini($data, $avui, $motivacioExcepcional);
        $tipus = $this->calendariService->validar($data, $iniciCurs, $fiCurs);
        $this->validarSaldo($profesor, $curs, $tipus, $iniciCurs, $fiCurs);
        $this->validarConsecutivitat($dni, $data, $iniciCurs, $fiCurs, $tipus);

        return [
            'data' => $data->toDateString(),
            'curs' => $curs,
            'tipus' => $tipus,
            'torn' => $this->tornService->delProfessor($dni),
            'saldo' => $this->saldo($dni, $curs, $tipus),
            'excepcional' => $data->lessThan($avui->addDays(7)),
        ];
    }

    /**
     * Retorna el curs aplicable al panell per a una data concreta.
     */
    public function cursVigent(CarbonInterface|string|null $data = null): string
    {
        $dia = $data === null ? CarbonImmutable::today() : $this->data($data);
        $iniciAny = $dia->month >= 9 ? $dia->year : $dia->year - 1;

        return sprintf('%d-%d', $iniciAny, $iniciAny + 1);
    }

    /**
     * Retorna els saldos legals i les reserves pendents del professor.
     *
     * @return array<string, array{disponible: float, pendent: int}>
     */
    public function resumSaldo(string $dni, ?string $curs = null): array
    {
        $curs ??= $this->cursVigent();
        $resum = [];

        foreach ([AssumpteParticular::TIPUS_LECTIU, AssumpteParticular::TIPUS_NO_LECTIU] as $tipus) {
            $resum[$tipus] = [
                'disponible' => $this->saldo($dni, $curs, $tipus),
                'pendent' => AssumpteParticular::query()
                    ->where('idProfesor', $dni)
                    ->where('curs', $curs)
                    ->where('tipus', $tipus)
                    ->where('estat', AssumpteParticular::ESTAT_PENDENT)
                    ->count(),
            ];
        }

        return $resum;
    }

    /**
     * Retorna el saldo disponible d'un tipus durant un curs.
     */
    public function saldo(string $dni, string $curs, string $tipus): float
    {
        $profesor = Profesor::query()->findOrFail($dni);
        [$inici, $fi] = $this->limitsDelCurs($curs);
        $limit = $this->saldoCalculator->limit($profesor, $inici, $fi);
        $consumits = AssumpteParticular::query()
            ->where('idProfesor', $dni)
            ->where('curs', $curs)
            ->where('tipus', $tipus)
            ->where('estat', AssumpteParticular::ESTAT_AUTORITZADA)
            ->count();

        return max(0.0, round($limit - $consumits, 4));
    }

    /**
     * Autoritza una petició amb la falta i el document firmat dins d'una transacció.
     */
    public function autoritzar(int $id, string $resoltaPer, string $documentFirmat): AssumpteParticular
    {
        if (blank($documentFirmat) || !Storage::disk('local')->exists($documentFirmat)) {
            throw new AssumpteParticularException(
                'No es pot autoritzar la petició sense la resolució oficial firmada.'
            );
        }

        try {
            $referencia = AssumpteParticular::query()->findOrFail($id);

            return DB::transaction(function () use (
                $id,
                $resoltaPer,
                $referencia,
                $documentFirmat
            ): AssumpteParticular {
                $peticionsDia = AssumpteParticular::query()
                    ->whereDate('data_gaudi', $referencia->data_gaudi->toDateString())
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();
                /** @var AssumpteParticular|null $peticio */
                $peticio = $peticionsDia->firstWhere('id', $id);
                if ($peticio === null || !$peticio->estaPendent()) {
                    throw new AssumpteParticularException('Només es pot autoritzar una petició pendent.');
                }

                [$iniciCurs, $fiCurs] = $this->limitsDelCurs($peticio->curs);
                $profesor = Profesor::query()->findOrFail($peticio->idProfesor);
                AssumpteParticular::query()
                    ->where('idProfesor', $peticio->idProfesor)
                    ->where('curs', $peticio->curs)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();
                $tipusActual = $this->calendariService->validar(
                    $peticio->data_gaudi,
                    $iniciCurs,
                    $fiCurs
                );
                if ($tipusActual !== $peticio->tipus) {
                    throw new AssumpteParticularException('El tipus del dia ha canviat en el calendari escolar.');
                }
                $this->validarSaldo($profesor, $peticio->curs, $peticio->tipus, $iniciCurs, $fiCurs);
                $this->validarConsecutivitat(
                    $peticio->idProfesor,
                    $peticio->data_gaudi,
                    $iniciCurs,
                    $fiCurs,
                    $peticio->tipus,
                    $peticio->id
                );
                $this->validarContingent($peticio, $peticionsDia);

                $falta = $this->faltaService->createForAssumpteParticular($peticio, $documentFirmat);
                $peticio->forceFill([
                    'estat' => AssumpteParticular::ESTAT_AUTORITZADA,
                    'resolta_per' => $resoltaPer,
                    'resolta_at' => now(),
                    'falta_id' => $falta->getKey(),
                    'resolucio_document' => $documentFirmat,
                ])->save();

                app(AssumpteParticularArchiveService::class)->arxivar($peticio, $profesor);

                return $peticio->fresh();
            }, 3);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($documentFirmat);
            throw $exception;
        }
    }

    /**
     * Denega motivadament una petició pendent.
     */
    public function denegar(int $id, string $resoltaPer, string $motiu): AssumpteParticular
    {
        if (blank($motiu)) {
            throw new AssumpteParticularException('La denegació ha d’estar motivada.');
        }

        return DB::transaction(function () use ($id, $resoltaPer, $motiu): AssumpteParticular {
            $peticio = AssumpteParticular::query()->lockForUpdate()->findOrFail($id);
            if (!$peticio->estaPendent()) {
                throw new AssumpteParticularException('Només es pot denegar una petició pendent.');
            }
            $peticio->forceFill([
                'estat' => AssumpteParticular::ESTAT_DENEGADA,
                'resolucio' => trim($motiu),
                'resolta_per' => $resoltaPer,
                'resolta_at' => now(),
            ])->save();

            return $peticio->fresh();
        });
    }

    /**
     * Cancel·la una petició pròpia mentre continua pendent.
     */
    public function cancelLar(int $id, string $dni): AssumpteParticular
    {
        return DB::transaction(function () use ($id, $dni): AssumpteParticular {
            $peticio = AssumpteParticular::query()->lockForUpdate()->findOrFail($id);
            if ($peticio->idProfesor !== $dni || !$peticio->estaPendent()) {
                throw new AssumpteParticularException('Només es pot cancel·lar una petició pròpia pendent.');
            }
            $peticio->forceFill([
                'estat' => AssumpteParticular::ESTAT_CANCEL_LADA,
                'cancel_lada_at' => now(),
            ])->save();

            return $peticio->fresh();
        });
    }

    /**
     * Valida que queda almenys un dia complet disponible.
     */
    private function validarSaldo(
        Profesor $profesor,
        string $curs,
        string $tipus,
        CarbonInterface $inici,
        CarbonInterface $fi
    ): void {
        $limit = $this->saldoCalculator->limit($profesor, $inici, $fi);
        $consumits = AssumpteParticular::query()
            ->where('idProfesor', $profesor->getKey())
            ->where('curs', $curs)
            ->where('tipus', $tipus)
            ->where('estat', AssumpteParticular::ESTAT_AUTORITZADA)
            ->count();

        if ($limit - $consumits < 1.0) {
            throw new AssumpteParticularException('No queda cap dia complet disponible d’aquest tipus.');
        }
    }

    /**
     * Impedix permisos lectius consecutius, inclòs divendres-dilluns.
     */
    private function validarConsecutivitat(
        string $dni,
        CarbonInterface $data,
        CarbonInterface $inici,
        CarbonInterface $fi,
        string $tipus,
        ?int $ignorarId = null
    ): void {
        if ($tipus !== AssumpteParticular::TIPUS_LECTIU) {
            return;
        }

        $altres = AssumpteParticular::query()
            ->where('idProfesor', $dni)
            ->where('tipus', AssumpteParticular::TIPUS_LECTIU)
            ->where('estat', AssumpteParticular::ESTAT_AUTORITZADA)
            ->when($ignorarId !== null, static fn ($query) => $query->whereKeyNot($ignorarId))
            ->get(['data_gaudi']);

        foreach ($altres as $altra) {
            if ($this->calendariService->sonLectiusConsecutius($data, $altra->data_gaudi, $inici, $fi)) {
                throw new AssumpteParticularException('No es poden gaudir dos dies lectius consecutius.');
            }
        }
    }

    /**
     * Valida el màxim global de huit i la part proporcional del torn.
     */
    private function validarContingent(AssumpteParticular $peticio, $peticionsDia): void
    {
        $autoritzades = $peticionsDia->where('estat', AssumpteParticular::ESTAT_AUTORITZADA);
        if ($autoritzades->count() >= ContingentAssumpteParticularCalculator::MAXIM_DIARI) {
            throw new AssumpteParticularException('Ja s’ha assolit el màxim de huit permisos per al dia.');
        }

        $quotes = $this->contingentCalculator->quotes($this->tornService->plantillaPerTorn());
        $quotaTorn = $quotes[$peticio->torn] ?? 0;
        if ($autoritzades->where('torn', $peticio->torn)->count() >= $quotaTorn) {
            throw new AssumpteParticularException('Ja s’ha assolit el contingent del torn per al dia.');
        }
    }

    /**
     * Valida la finestra d'un mes i l'excepció de menys de set dies.
     */
    private function validarTermini(CarbonInterface $data, CarbonInterface $hui, ?string $motivacio): void
    {
        if (!$data->greaterThan($hui)) {
            throw new AssumpteParticularException('La data de gaudi ha de ser posterior a hui.');
        }
        if ($data->greaterThan($hui->addMonthNoOverflow())) {
            throw new AssumpteParticularException('La petició no es pot presentar amb més d’un mes d’antelació.');
        }
        if ($data->lessThan($hui->addDays(7)) && blank($motivacio)) {
            throw new AssumpteParticularException('Cal motivar les peticions presentades amb menys de set dies naturals.');
        }
    }

    /**
     * @return array{string, CarbonImmutable, CarbonImmutable}
     */
    private function cursDe(CarbonInterface $data): array
    {
        if ($data->month === 8) {
            throw new AssumpteParticularException('Agost està fora del període de còmput del curs.');
        }
        $iniciAny = $data->month >= 9 ? $data->year : $data->year - 1;
        $curs = sprintf('%d-%d', $iniciAny, $iniciAny + 1);
        [$inici, $fi] = $this->limitsDelCurs($curs);

        return [$curs, $inici, $fi];
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

    /**
     * Normalitza una data d'entrada.
     */
    private function data(CarbonInterface|string $data): CarbonImmutable
    {
        return $data instanceof CarbonInterface
            ? CarbonImmutable::instance($data)->startOfDay()
            : CarbonImmutable::parse($data)->startOfDay();
    }
}
