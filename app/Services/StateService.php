<?php
namespace Intranet\Services;

use function config, getClass, getClase;

class StateService
{
    private $element;
    private $statesElement;

    public function __construct($class, $id = null)
    {
        if (is_string($class)) {
            $this->element = $id ? $class::find($id) : new $class;
            $this->statesElement = config('modelos.' . getClass($class));
        } else {
            $this->element = $class;
            $this->statesElement = config('modelos.' . getClase($class));
        }
    }

    public function putEstado($estado, $mensaje = null, $fecha = null)
    {
        if (!$this->element) {
            return false; // Retornem false en lloc de JSON per facilitar la gestió d'errors
        }

        if ($fecha !== null) {
            $this->makeDocument();
            $this->dateResolve($fecha, $mensaje);
        }

        $this->element->estado = $estado;

        if (!$this->element->save()) {
            return false; // Retornem false si hi ha un error en el save()
        }

        AdviseService::exec($this->element, $mensaje);

        return $this->element->estado;
    }

    private function makeDocument()
    {
        if (!isset($this->element->fichero) || empty($this->element->fichero)) {
            return;
        }

        $gestor = new GestorService($this->element);
        $gestor->save([
            'tipoDocumento' => getClase($this->element),
            'rol' => '2',
        ]);
    }

    private function dateResolve($fecha, $mensaje)
    {
        if (isset($this->element->fechasolucion)) {
            $this->element->fechasolucion = $fecha;
        }
        if (isset($this->statesElement['mensaje']) && $mensaje) {
            $field = $this->statesElement['mensaje'];
            $this->element->$field = $mensaje;
        }
    }

    public function resolve($mensaje = null)
    {
        $estado = $this->statesElement['resolve'] ?? null;
        if (!$estado) {
            return false; // Si no hi ha estat de resolució definit, retornem false
        }
        return $this->putEstado($estado, $mensaje, hoy());
    }

    public function refuse($mensaje = null)
    {
        $estado = $this->statesElement['refuse'] ?? null;
        if (!$estado) {
            return false;
        }
        return $this->putEstado($estado, $mensaje);
    }

    public function _print()
    {
        $printState = $this->statesElement['print'] ?? null;
        $resolveState = $this->statesElement['resolve'] ?? null;

        if (!$printState) {
            return false; // Si no hi ha estat definit per imprimir, retornem false
        }

        if ($printState == $resolveState) {
            return $this->putEstado($printState, '', hoy());
        }

        return $this->putEstado($printState);
    }

    public function getEstado()
    {
        return $this->element ? $this->element->estado : null;
    }
}
