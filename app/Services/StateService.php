<?php
namespace Intranet\Services;

use function config,getClass,getClase;


class StateService
{
    private $element;
    private $statesElement;

    /**
     * @param $id
     */
    public function __construct($class,$id=null)
    {
        if (is_string($class)){
            $this->element = $class::find($id);
            $this->statesElement = config('modelos.'.getClass($class));
        } else {
            $this->element = $class;
            $this->statesElement = config('modelos.'.getClase($class));
        }
    }



    public function putEstado($estado, $mensaje = null, $fecha = null)
    {
        if ($fecha != null) {
            $this->makeDocument();
            $this->dateResolve($fecha);
        }

        $this->element->estado = $estado;
        $this->element->save();

        AdviseService::exec($this->element,$mensaje);

        return ($this->element->estado);
    }

    private function makeDocument(){
        if ($this->element->fichero != ''){
            $gestor = new GestorService($this->element);
            $gestor->save([
                'tipoDocumento' => getClase($this->element),
                'rol'=> '2',
            ]);
        }
    }

    private function dateResolve($fecha){
        if (isset($this->element->fechasolucion)) {
            $this->element->fechasolucion = $fecha;
        }
    }

    public function resolve($mensaje = null)
    {
        return $this->putEstado($this->statesElement['resolve'], $mensaje, Hoy());
    }

    public function refuse($mensaje = null)
    {
        return $this->putEstado($this->statesElement['refuse'], $mensaje);
    }

    public function _print()
    {
        if ( $this->statesElement['print'] == $this->statesElement['resolve']) {
            return $this->putEstado($this->statesElement['print'], '', Hoy());
        }  else {
            return $this->putEstado($this->statesElement['print']);
        }
    }

    public function getEstado()
    {
        return $this->element->estado;
    }

}