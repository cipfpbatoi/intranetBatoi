<?php


namespace Intranet\Services;


use Intranet\Entities\OrdenReunion;
use Intranet\Entities\TipoReunion;

/**
 * Servei MeetingOrderGenerateService.
 */
class MeetingOrderGenerateService
{
    /** @var mixed */
    private $reunion;
    /** @var mixed */
    private $tipo;

    public function __construct($reunion)
    {
        $this->reunion = $reunion;
        $this->tipo = $reunion->Tipos();
    }

    public function exec()
    {
        $contador = 1;

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

                $this->storeItem($contador, $texto, $resumenText);
            }
        }
    }

    private function isOrderAdvanced($texto)
    {
        return strpos($texto, '->') !== false;
    }

    private function storeAdvancedItems($query, $resumen, &$contador)
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

    private function getResumenAdvanced($query, $asArray = false)
    {
        $descomposed = explode('->', $query, 3);
        $class = "Intranet\\Entities\\" . $descomposed[0];
        $method = $descomposed[1];
        $field = $descomposed[2];

        $results = $class::$method()->get()->pluck($field);

        return $asArray ? $results->toArray() : $results->implode(', ');
    }

    private function storeItem(&$contador, $text, $resumen)
    {
        $orden = new OrdenReunion();
        $orden->idReunion = $this->reunion->id;
        $orden->orden = $contador++;
        $orden->descripcion = $text;
        $orden->resumen = $resumen;
        $orden->save();
    }
}