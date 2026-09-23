<?php

declare(strict_types=1);

namespace Intranet\Services\Calendar;

use Intranet\Application\Reunion\ReunionContinuityService;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;
use Intranet\Services\Document\TipoReunionService;

/**
 * Genera els punts inicials d'una reunió i aplica la continuïtat entre actes.
 */
class MeetingOrderGenerateService
{
    private Reunion $reunion;

    private TipoReunionService $tipo;

    /** @var array<string, string> */
    private array $inheritedSummaries = [];

    /**
     * Crea el generador per a una reunió concreta.
     */
    public function __construct(
        Reunion $reunion,
        private readonly ?ReunionContinuityService $continuityService = null
    ) {
        $this->reunion = $reunion;
        $this->tipo = $reunion->Tipos();
    }

    /**
     * Crea tots els punts configurats per al tipus de reunió.
     */
    public function exec(): void
    {
        $contador = 1;
        $this->inheritedSummaries = $this->continuity()->inheritedSummaries($this->reunion);

        foreach ($this->tipo->ordenes as $key => $texto) {
            if (is_array($this->tipo->resumen)) {
                $resumen = $this->tipo->resumen[$key] ?? '';
            } else {
                $resumen = $this->tipo->resumen;
            }

            if ($this->isOrderAdvanced($texto)) {
                $this->storeAdvancedItems($texto, $resumen, $contador);
            } else {
                if ($this->isOrderAdvanced($resumen)) {
                    $resumenText = $this->getResumenAdvanced($resumen);
                } else {
                    $resumenText = $resumen;
                }

                $this->storeItem(
                    $contador,
                    $texto,
                    $this->inheritedSummaries[$texto] ?? $resumenText
                );
            }
        }
    }

    /**
     * Indica si la configuració conté una consulta dinàmica.
     */
    private function isOrderAdvanced(mixed $texto): bool
    {
        return is_string($texto) && str_contains($texto, '->');
    }

    /**
     * Genera punts a partir d'una consulta configurada.
     */
    private function storeAdvancedItems(string $query, mixed $resumen, int &$contador): void
    {
        $descomposedQuery = explode('->', $query, 3);
        $class = "Intranet\\Entities\\" . $descomposedQuery[0];
        $funcion = $descomposedQuery[1];
        $campo = $descomposedQuery[2];

        // Si resumen també és dinàmic
        $resumenEsAvançat = $this->isOrderAdvanced($resumen);
        if ($resumenEsAvançat) {
            $resumenResults = $this->getResumenAdvanced($resumen, true);
        }

        foreach ($class::$funcion()->get() as $index => $element) {
            $resumenText = $resumenEsAvançat
                ? ($resumenResults[$index] ?? '')
                : ($resumen !== null ? $resumen . ' ' . ($index + 1) : '');

            $this->storeItem(
                $contador,
                $element->$campo,
                $resumenText
            );
        }
    }

    /**
     * Resol el resum dinàmic configurat per al punt.
     */
    private function getResumenAdvanced(string $query, bool $asArray = false): mixed
    {
        $descomposed = explode('->', $query, 3);
        $class = "Intranet\\Entities\\" . $descomposed[0];
        $method = $descomposed[1];
        $field = $descomposed[2];

        $results = $class::$method()->get()->pluck($field);

        return $asArray ? $results->toArray() : $results->implode(', ');
    }

    /**
     * Persistix un punt amb un resum sempre auditable.
     */
    private function storeItem(int &$contador, string $text, mixed $resumen): void
    {
        $orden = new OrdenReunion();
        $orden->idReunion = $this->reunion->id;
        $orden->orden = $contador++;
        $orden->descripcion = $text;
        $orden->resumen = $this->continuity()->normaliseSummary($resumen);
        $orden->save();
    }

    /**
     * Resol el servei de continuïtat injectable.
     */
    private function continuity(): ReunionContinuityService
    {
        return $this->continuityService ?? app(ReunionContinuityService::class);
    }
}
