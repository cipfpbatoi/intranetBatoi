<?php

namespace Intranet\Sao;

use Exception;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Services\AttachedFileService;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Signatura;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class Annexes
{


    public static function index($driver)
    {
        try {
            $alumnes = [];
            foreach (AlumnoFctAval::realFcts()
                         ->where('beca', 0)
                         ->whereNotNull('idSao')
                         ->activa()
                         ->where('pg0301', 0)
                         ->get() as $fct) {
                $find = Adjunto::where('size', 1024)->where('route', 'alumnofctaval/'.$fct->id)->count();
                if ($fct->idSao) {
                    if (!$find) {
                        $driver->navigate()->to(
                            "https://foremp.edu.gva.es/inc/fcts/documentos_fct.php?id={$fct->idSao}&documento=2"
                        );
                        sleep(1);
                        try {
                            $name = trim(
                                $driver->findElement(
                                    WebDriverBy::cssSelector(
                                        "table.tablaListadoFCTs tbody tr:nth-child(2) td:nth-child(1)"
                                    )
                                )->getText()
                            );
                            $onclick = $driver->findElement(
                                WebDriverBy::cssSelector(".botonSelec[value='Descargar']")
                            )->getAttribute('onclick');
                            $cut1 = explode("'", $onclick);
                            AttachedFileService::saveLink(
                                $name,
                                "https://foremp.edu.gva.es/".$cut1[1],
                                'SAO:Annexe II i III',
                                'zip',
                                "alumnofctaval/$fct->id"
                            );
                            // esborrar fitxers de signatura
                            foreach (Signatura::where('idSao', $fct->idSao)->get() as $signatura) {
                                $signatura->deleteFile();
                                $signatura->delete();
                            }
                            $alumnes[] = $fct->Alumno->shortName;
                        } catch (Exception $e) {
                            Alert::info("Annexes de ".$fct->Alumno->fullName." no trobats");
                        }
                        try {
                            $driver->findElement(WebDriverBy::cssSelector(".botonSelec[value='Cerrar']"))->click();
                            sleep(1);
                        } catch (Exception $e) {
                            Alert::info("Fct de ".$fct->Alumno->fullName." no trobada. Esborra-la de la intranet");
                        }
                    } else {
                        Alert::info("Annexes de ".$fct->Alumno->fullName." ja descarregats");
                    }
                }
            }
            arrayAlert($alumnes, 'Annexes Baixats: ');
        } catch (Exception $e) {
            Alert::danger($e);
        }
        $driver->quit();
        return back();
    }

    protected static function alertSuccess(array $alumnes, $message='Sincronitzades Fcts: ')
    {
        if (count($alumnes)) {
            $tots = '';
            foreach ($alumnes as $alumne) {
                $tots .= $alumne.', ';
            }
            Alert::info($message.$tots);
        }
    }

}
