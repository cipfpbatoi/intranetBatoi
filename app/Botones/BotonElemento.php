<?php

namespace Intranet\Botones;
use Illuminate\Support\Facades\Storage;

abstract class BotonElemento extends Boton
{
    // d'ella heren tots els botons meyns el basic
    
    // mostra el boto
    // afegix condicio per mostrar-lo
    public function show($elemento = null, $key = null)
    {
        if ($this->esVisible($elemento))
            return parent::show($elemento, $key);
    }

    // mira si compleix l'element compleix les condicions del where
    private function esVisible($elemento)
    {
        if ($this->where != '') {
            $condiciones = [];
            for ($i = 0; $i < count($this->where); $i = $i + 3) {
                $camp = $this->where[$i];
                $condiciones[] = $this->condicion($elemento->$camp, $this->where[$i + 1], $this->where[$i + 2]);
            }
            $result = true;
            foreach ($condiciones as $condicion) {
                $result = $result && $condicion;
            }
            return ($result);
        } elseif ($this->orWhere != '') {
                $condiciones = [];
                for ($i = 0; $i < count($this->orWhere); $i = $i + 3) {
                    $camp = $this->orWhere[$i];
                    $condiciones[] = $this->condicion($elemento->$camp, $this->orWhere[$i + 1], $this->orWhere[$i + 2]);
                }
                $result = false;
                foreach ($condiciones as $condicion) {
                    $result = $result || $condicion;
                }
                return ($result);
            
            } else return true;
    }

    // condicions possibles
    private function condicion($elemento, $op, $valor)
    {
        if ($op == 'anterior') {
            $elemento = Fecha($elemento);
            $valor = Fecha($valor);
            $op = "<=";
        }
        if ($op == 'posterior') {
            $elemento = Fecha($elemento);
            $valor = Fecha($valor);
            $op = ">";
        }
        if ($op == 'in') {
            return (in_array($elemento, $valor));
        }
        if ($op == 'isNNull'){
           return (!is_null($elemento));
        }
        if ($op == 'isNull')
            return (is_null($elemento));
        if ($op == 'existe'){
            return Storage::disk('local')->exists(str_replace('$', $elemento, $valor));
        }
        if ($op == 'noExiste'){
            return !Storage::disk('local')->exists(str_replace('$', $elemento, $valor));
        }
        $condicion = "return('$elemento' $op '$valor');";
        return eval($condicion) ? true : false;
    }

}
