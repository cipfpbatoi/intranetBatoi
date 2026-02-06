<?php

namespace Intranet\Botones;

use Illuminate\Support\Facades\Storage;

/**
 * Botó associat a un element, amb visibilitat condicionada.
 */
abstract class BotonElemento extends Boton
{
    /**
     * Mostra el botó si compleix les condicions de visibilitat.
     */
    public function show($elemento = null, $key = null): string
    {
        if ($this->isVisible($elemento)) {
            return parent::show($elemento, $key);
        }
        return '';
    }

    /**
     * Retorna el botó renderitzat si compleix les condicions.
     */
    public function render($elemento = null )
    {
        if ($this->isVisible($elemento)) {
            return parent::render($elemento );
        }
        return '';
    }

    /**
     * Avalua si l'element compleix les condicions de visibilitat.
     */
    private function isVisible($elemento)
    {
        if ($this->where != '') {
            return $this->avalAndConditions($this->extractConditions($elemento, 'where'));
        }
        if ($this->orWhere != '') {
            return $this->avalOrConditions($this->extractConditions($elemento, 'orWhere'));
        }

        return true;
    }

    /**
     * Extreu i avalua les condicions configurades.
     */
    private function extractConditions($elemento, $condicio)
    {
        $condiciones = [];

        for ($i = 0, $iMax = count($this->$condicio); $i < $iMax; $i = $i + 3) {

            $campo = $this->$condicio[$i];
            $condiciones[] = $this->avalCondition(
                $elemento->$campo,
                $this->$condicio[$i + 1],
                $this->$condicio[$i + 2]
            );
        }

        return $condiciones;
    }

    /**
     * Avalua condicions amb AND.
     */
    private function avalAndConditions($conditions)
    {
        $result = true;
        foreach ($conditions as $condition) {
            $result = $result && $condition;
        }
        return $result;
    }

    /**
     * Avalua condicions amb OR.
     */
    private function avalOrConditions($conditions)
    {
        $result = false;
        foreach ($conditions as $condition) {
            $result = $result || $condition;
        }
        return $result;
    }


    /**
     * Avalua una condició individual.
     */
    private function avalCondition($elemento, $op, $valor)
    {
        $op = is_string($op) ? trim($op) : $op;

        if ($op == 'anterior') {
            $elemento = fecha($elemento);
            $valor = fecha($valor);
            $op = "<=";
        }
        if ($op == 'posterior') {
            $elemento = fecha($elemento);
            $valor = fecha($valor);
            $op = ">";
        }
        if ($op == 'in') {
            return (in_array($elemento, $valor));
        }
        if ($op == 'isNNull') {
           return (!is_null($elemento));
        }
        if ($op == 'isNull') {
            return (is_null($elemento));
        }
        if ($op == 'existe') {
            return Storage::disk('local')->exists(str_replace('$', $elemento, $valor));
        }
        if ($op == 'noExiste') {
            return !Storage::disk('local')->exists(str_replace('$', $elemento, $valor));
        }
        return match ($op) {
            '==' => $elemento == $valor,
            '!=' => $elemento != $valor,
            '<>' => $elemento != $valor,
            '>' => $elemento > $valor,
            '>=' => $elemento >= $valor,
            '<' => $elemento < $valor,
            '<=' => $elemento <= $valor,
            default => false,
        };
    }

}
