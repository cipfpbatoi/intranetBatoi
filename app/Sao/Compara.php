<?php

namespace Intranet\Sao;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Http\Request;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use Intranet\Services\SeleniumService;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class Compara
{
    const TD_NTH_CHILD_2 = "td:nth-child(2)";
    const TR_NTH_CHILD_2 = "tr:nth-child(2)";
    const TD_NTH_CHILD_3 = "td:nth-child(3)";
    const TD_NTH_CHILD_4 = "td:nth-child(4)";

    public function compara(Request $request)
    {
        $dades = session('dades');
        foreach ($request->request as $key => $value) {
            if ($value == 'sao') {
                list($modelo,$field,$idFct,$tipo) = self::descomposaClau($key);
                $modelo->$field = $dades[$idFct][$tipo][$field]['sao'];
                $modelo->save();
            }
        }
        return redirect(route('alumnofct.index'));
    }

    private static function igual($intranet, $sao)
    {
        if (
            trim(strtolower(eliminarTildes($intranet))) == trim(strtolower(eliminarTildes($sao))) ||
            $sao == '' || $sao == ' ' || $sao == 'Alcoy'
        ) {
            return null;
        }
        return array('intranet'=>$intranet,'sao'=>$sao);
    }

    public static function index($driver)
    {
        $dades = array();
        try {
            foreach (AlumnoFct::misFcts()->whereNotNull('idSao')->get() as $fctAl) {
                try {
                    $fct = $fctAl->Fct;
                    $centro = $fct->Colaboracion->Centro;
                    $empresa = $centro->Empresa;
                    if (!isset($dades[$fct->id]['empresa']['idEmpresa'])) {
                        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=10&idFct=$fctAl->idSao");
                        sleep(1);
                        $dades[$fct->id]['nameEmpresa'] = $empresa->nombre;
                        $dades[$fct->id]['nameCentro'] = $centro->nombre;
                        $dades[$fct->id]['empresa']['idEmpresa'] =
                            $driver->findElement(WebDriverBy::cssSelector('#empresaFCT'))
                                ->getAttribute('value');
                        $dades[$fct->id]['empresa']['concierto'] =
                            self::igual(
                                $empresa->concierto,
                                $driver->findElement(WebDriverBy::cssSelector('#numConciertoEmp'))
                                    ->getAttribute('value'));

                        $dadesEmpresa = $driver
                            ->findElement(WebDriverBy::cssSelector("td#celdaDatosEmpresa table.infoCentroBD tbody"));
                        $detallesEmpresa = $dadesEmpresa->findElement(WebDriverBy::cssSelector(self::TR_NTH_CHILD_2));
                        $dades[$fct->id]['empresa']['cif'] = self::igual(
                            $empresa->cif,
                            $detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(1)"))->getText());
                        $dades[$fct->id]['empresa']['nombre'] = self::igual(
                            $empresa->nombre,
                            $detallesEmpresa->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_2))->getText());
                        $dades[$fct->id]['empresa']['direccion'] = self::igual(
                            $empresa->direccion,
                            $detallesEmpresa->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_3))->getText());
                        $dades[$fct->id]['empresa']['localidad'] = self::igual(
                            $empresa->localidad,
                            $detallesEmpresa->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_4))->getText());
                        $detallesEmpresa = $dadesEmpresa->findElement(WebDriverBy::cssSelector("tr:nth-child(4)"));
                        $dades[$fct->id]['empresa']['telefono'] = self::igual(
                            $empresa->telefono,
                            $detallesEmpresa->findElement(WebDriverBy::cssSelector("td:nth-child(1)"))->getText());
                        $dades[$fct->id]['empresa']['gerente'] = self::igual(
                            $empresa->gerente,
                            $detallesEmpresa->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_2))->getText());
                        $dades[$fct->id]['empresa']['actividad'] = self::igual(
                            $empresa->actividad,
                            $detallesEmpresa->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_3))->getText());
                        $dades[$fct->id]['empresa']['email'] = self::igual(
                            $empresa->email,
                            $detallesEmpresa->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_4))->getText());

                        $dadesCentro = $driver->findElement(
                            WebDriverBy::cssSelector("td#celdaDatosCT table.infoCentroBD tbody")
                        );
                        $detallesCentro = $dadesCentro->findElement(WebDriverBy::cssSelector(self::TR_NTH_CHILD_2));
                        $dades[$fct->id]['centro']['nombre'] = self::igual(
                            $centro->nombre,
                            $detallesCentro->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_2))->getText()
                        );
                        $dades[$fct->id]['centro']['localidad'] = self::igual(
                            $centro->localidad,
                            $detallesCentro->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_3))->getText()
                        );
                        $dades[$fct->id]['centro']['telefono'] = self::igual(
                            $centro->telefono,
                            $detallesCentro->findElement(WebDriverBy::cssSelector(self::TD_NTH_CHILD_4))->getText()
                        );
                        $dades[$fct->id]['centro']['email'] = self::igual(
                            $centro->email,
                            $detallesCentro->findElement(WebDriverBy::cssSelector("td:nth-child(6)"))->getText()
                        );
                        $dades[$fct->id]['centro']['horarios'] = self::igual(
                            $centro->horarios,
                            $driver->findElement(
                                WebDriverBy
                                    ::cssSelector("table.tablaDetallesFCT tbody tr:nth-child(14) td:nth-child(2)")
                            )->getText()
                        );
                        $driver->findElement(
                            WebDriverBy::cssSelector("button.botonRegistro[value='Registrarse']")
                        )->click();
                        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=34&idCT=$centro->idSao");
                        sleep(1);
                        $dades[$fct->id]['centro']['direccion'] = self::igual(
                            $centro->direccion,
                            $driver->findElement(
                                WebDriverBy::cssSelector("input.campoAlumno[name='direccion'")
                            )->getAttribute('value')
                        );
                        $dades[$fct->id]['centro']['codiPostal'] = self::igual(
                            $centro->codiPostal,
                            $driver->findElement(
                                WebDriverBy::cssSelector("input.campoAlumno[name='cp'")
                            )->getAttribute('value')
                        );
                    }
                } catch (NoSuchElementException $e) {
                    Alert::warning('Element no trobat');
                }

            }
        } catch (Exception $e) {
            Alert::danger($e);
        }
        $driver->close();
        if (count($dades)) {
            session(compact('dades'));
            return view('sao.compara', compact('dades'));
        } else {
            return back();
        }
    }

    private static function descomposaClau($clau)
    {
        $keyDescomposada = explode('_', $clau);
        $field = $keyDescomposada[2];
        $idFct = $keyDescomposada[1];
        $tipo = $keyDescomposada[0];
        $modelo = ($tipo == 'centro')?
            Fct::find($idFct)->Colaboracion->Centro:
            Fct::find($idFct)->Colaboracion->Centro->Empresa;
        return array($modelo,$field,$idFct,$tipo);

    }

}
