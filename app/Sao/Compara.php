<?php

namespace Intranet\Sao;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\AlumnoFct;
use Styde\Html\Facades\Alert;

class Compara
{
    private RemoteWebDriver $driver;


    const TD_NTH_CHILD_2 = "td:nth-child(2)";
    const TR_NTH_CHILD_2 = "tr:nth-child(2)";
    const TD_NTH_CHILD_3 = "td:nth-child(3)";
    const TD_NTH_CHILD_4 = "td:nth-child(4)";

    public function __construct(RemoteWebDriver $driver)
    {
        $this->driver = $driver;
    }



    public function index()
    {
        $dades = [];

        try {
            $this->processFcts($dades);
        } catch (Exception $e) {
            Alert::danger("Error general: " . $e->getMessage());
        } finally {
            $this->driver->quit();
        }

        if (count($dades)) {
            session(compact('dades'));
            return view('sao.compara', compact('dades'));
        }

        return back();
    }

    private function processFcts(&$dades)
    {
        foreach ($this->getValidFcts() as $fctAl) {
            try {
                $this->processFct($fctAl, $dades);
            } catch (Exception $e) {
                Alert::warning("Error en {$fctAl->Alumno->shortName}: " . $e->getMessage());
            }
        }
    }

    private function getValidFcts()
    {
        return AlumnoFct::misFcts()->whereNotNull('idSao')->get();
    }

    private function processFct($fctAl, &$dades)
    {
        $fct = $fctAl->Fct;
        $centro = $fct->Colaboracion->Centro;
        $empresa = $centro->Empresa;

        if (!isset($dades[$fct->id]['empresa']['idEmpresa'])) {
            $this->driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=10&idFct=$fctAl->idSao");

            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#empresaFCT'))
            );

            $dades[$fct->id]['nameEmpresa'] = $empresa->nombre;
            $dades[$fct->id]['nameCentro'] = $centro->nombre;
            $dades[$fct->id]['empresa']['idEmpresa'] =
                $this->driver->findElement(WebDriverBy::cssSelector('#empresaFCT'))->getAttribute('value');

            $dades[$fct->id]['empresa']['concierto'] =
                $this->compararValor($empresa->concierto, '#numConciertoEmp', "empresa.concierto.$empresa->id");

            $dadesEmpresa = $this->driver->findElement(WebDriverBy::cssSelector("td#celdaDatosEmpresa table.infoCentroBD tbody"));
            $detallesEmpresa = $dadesEmpresa->findElement(WebDriverBy::cssSelector(self::TR_NTH_CHILD_2));

            $this->processEmpresaDetails($empresa, $detallesEmpresa, $dades[$fct->id]);

            $dadesCentro = $this->driver->findElement(WebDriverBy::cssSelector("td#celdaDatosCT table.infoCentroBD tbody"));
            $detallesCentro = $dadesCentro->findElement(WebDriverBy::cssSelector(self::TR_NTH_CHILD_2));

            $this->processCentroDetails($centro, $detallesCentro, $dades[$fct->id]);
        }
    }

    private function processEmpresaDetails($empresa, $detallesEmpresa, &$dades)
    {
        $dades['empresa']['cif'] = $this->compararValor($empresa->cif, "td:nth-child(1)", "empresa.cif.$empresa->id");
        $dades['empresa']['nombre'] = $this->compararValor($empresa->nombre, self::TD_NTH_CHILD_2, "empresa.nombre.$empresa->id");
        $dades['empresa']['direccion'] = $this->compararValor($empresa->direccion, self::TD_NTH_CHILD_3, "empresa.direccion.$empresa->id");
        $dades['empresa']['localidad'] = $this->compararValor($empresa->localidad, self::TD_NTH_CHILD_4, "empresa.localidad.$empresa->id");
    }

    private function processCentroDetails($centro, $detallesCentro, &$dades)
    {
        $dades['centro']['nombre'] = $this->compararValor($centro->nombre, self::TD_NTH_CHILD_2, "centro.nombre.$centro->id");
        $dades['centro']['localidad'] = $this->compararValor($centro->localidad, self::TD_NTH_CHILD_3, "centro.localidad.$centro->id");
        $dades['centro']['telefono'] = $this->compararValor($centro->telefono, self::TD_NTH_CHILD_4, "centro.telefono.$centro->id");
    }

    private function compararValor($valorLocal, $selector, $clau)
    {
        try {
            $element = $this->driver->findElement(WebDriverBy::cssSelector($selector));
            $valorSao = $element->getText();

            // Debugging
             Log::info("Comparació de valors - Clau: $clau | Intranet: $valorLocal | SAO: $valorSao");

            return $this->igual($valorLocal, $valorSao, $clau);
        } catch (NoSuchElementException $e) {
             Log::error("No s'ha trobat l'element per a $clau ($selector)");
            return null;
        }
    }

    private function igual($intranet, $sao, $clau = null)
    {
         Log::info("Funció igual() - Clau: $clau | Intranet: $intranet | SAO: $sao");

        if (
            trim(strtolower(eliminarTildes($intranet))) == trim(strtolower(eliminarTildes($sao))) ||
            in_array($sao, ['', ' ', 'Alcoy', 'Pendiente'])
        ) {
            return null;
        }

        if ($clau && $this->esBuida($intranet) && $this->actualitzaBuida($clau, $sao)) {
            return null;
        }

        return ['intranet' => $intranet, 'sao' => $sao];
    }

    private function esBuida($clau)
    {
        return empty($clau) || trim($clau) === '';
    }

    private function actualitzaBuida($clau, $valueToUpdate)
    {
        $parts = explode(".", $clau);
        $modelInstance = app("Intranet\\Entities\\" . ucfirst($parts[0]));
        $entityToUpdate = $modelInstance::find($parts[2]);

        if ($entityToUpdate) {
            $entityToUpdate->{$parts[1]} = $valueToUpdate;
            $entityToUpdate->save();
            return true;
        }
        return false;
    }
}