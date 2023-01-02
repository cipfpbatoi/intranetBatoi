<?php

namespace Intranet\Botones;

use Illuminate\Support\Facades\Storage;

abstract class BotonElemento extends Boton
{

    public function show($elemento = null, $key = null)
    {
        if ($this->isVisible($elemento)) {
            return parent::show($elemento, $key);
        }
    }

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

    private function extractConditions($elemento, $condicio)
    {
        $condiciones = [];

        for ($i = 0; $i < count($this->$condicio); $i = $i + 3) {

            $campo = $this->$condicio[$i];
            $condiciones[] = $this->avalCondition(
                $elemento->$campo,
                $this->$condicio[$i + 1],
                $this->$condicio[$i + 2]
            );
        }

        return $condiciones;
    }

    private function avalAndConditions($conditions)
    {
        $result = true;
        foreach ($conditions as $condition) {
            $result = $result && $condition;
        }
        return $result;
    }

    private function avalOrConditions($conditions)
    {
        $result = true;
        foreach ($conditions as $condition) {
            $result = $result || $condition;
        }
        return $result;
    }


    private function avalCondition($elemento, $op, $valor)
    {
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
        $condicion = "return('$elemento' $op '$valor');";
        return eval($condicion) ? true : false;
    }

}
