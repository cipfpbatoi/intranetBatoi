<?php


namespace Intranet\Services;


use Intranet\Entities\OrdenReunion;
use Intranet\Entities\TipoReunion;

class MeetingOrderGenerateService
{
        private $reunion;
        private $tipo;


        public function __construct($reunion){
            $this->reunion = $reunion;
            $this->tipo = $reunion->tipo;
        }

        public function exec(){
            $contador = 1;
            foreach (TipoReunion::ordenes($this->tipo) as $key => $texto) {
                if ($this->isOrderAdvanced($texto)) {
                    $this->storeAdvancedItems($texto);
                }
                else {
                    $this->storeItem( $contador, $texto,  TipoReunion::resumen($this->tipo)[$key] ?? '');
                }
            }
        }

        private function isOrderAdvanced($texto){
            return strpos($texto, '->');
        }



    private function storeAdvancedItems($query){
        $contador = 1;
        $descomposedQuery = explode('->', $query,3);
        $class = "Intranet\\Entities\\". $descomposedQuery[0];
        $funcion = $descomposedQuery[1];
        $campo = $descomposedQuery[2];
        foreach ($class::$funcion()->get() as $element) {
            $this->storeItem($contador, $element->$campo, TipoReunion::resumen($this->tipo) != null ? TipoReunion::resumen($this->tipo) . $contador : '');
        }
    }

    private function storeItem(&$contador,$text,$resumen){
        $orden = new OrdenReunion();
        $orden->idReunion = $this->reunion->id;
        $orden->orden = $contador++;
        $orden->descripcion = $text;
        $orden->resumen = $resumen;
        $orden->save();
    }
}