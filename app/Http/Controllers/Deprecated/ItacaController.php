<?php

namespace Intranet\Http\Controllers\Deprecated;

use Carbon\Carbon;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\Actividad;
use Intranet\Entities\Falta;
use Intranet\Exceptions\IntranetException;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Requests\PasswordRequest;
use Intranet\Services\School\ItacaService;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Controlador legacy d'integració amb ITACA conservat fora del flux actiu.
 *
 * Mantingut només com a referència i possible punt de recuperació futura.
 * No té rutes web actives en Sprint 5.
 *
 * @deprecated No forma part del flux actual de l'aplicació.
 */
class ItacaController extends Controller
{
    /**
     * Enviament d'activitats extraescolars a ITACA.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function extraescolars(Request $request)
    {
        $activitats = Actividad::where('estado', 4)->get();
        $total = count($activitats);

        if ($total == 0) {
            Alert::info('No hi ha faltas valorades');
            return back();
        }

        try {
            $dni = authUser()->dni;
            $itacaService = new ItacaService($dni, $request->password);

            try {
                $itacaService->goToLlist();
            } catch (NoSuchElementException $e) {
                $itacaService->close();
                Alert::danger('No he pogut accedir al llistat. Potser la sessió ha expirat o el login ha fallat.');
                return back();
            }
        } catch (IntranetException $e) {
            Alert::danger('No he pogut loguejar-me: ' . $e->getMessage());
            return back();
        }

        $count = 0;
        $failures = 0;

        foreach ($activitats as $activitat) {
            if ($itacaService->processActivitat($activitat)) {
                $count++;
            } else {
                try {
                    $itacaService->goToLlist();
                } catch (IntranetException | NoSuchElementException $e) {
                    Alert::danger($e->getMessage());
                    $itacaService->close();
                    Alert::info("$count extraescolars actualitzades, $failures errors de $total");
                    return back();
                }
                $failures++;
            }
        }

        $itacaService->close();
        Alert::info("$count activitats actualitzades, $failures errors de $total");
        return back();
    }

    /**
     * Enviament de faltes completes a ITACA.
     *
     * @param PasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function faltes(PasswordRequest $request)
    {
        try {
            $itacaService = new ItacaService(authUser()->dni, $request->password);
        } catch (IntranetException $e) {
            Alert::danger($e->getMessage());
            return back();
        }

        $count = 0;
        $failures = 0;

        try {
            $list1 = Falta::where('estado', 3)
                ->where('itaca', 0)
                ->where('dia_completo', 1)
                ->whereColumn('desde', 'hasta')
                ->whereMonth('hasta', $request->month)
                ->get();

            $list2 = Falta::where('estado', 3)
                ->where('itaca', 0)
                ->where('dia_completo', 1)
                ->whereRaw('DATEDIFF(hasta, desde) BETWEEN 1 AND 3')
                ->whereMonth('hasta', $request->month)
                ->get();

            foreach ($list1 as $falta) {
                $fecha = Carbon::parse($falta->desde)->format('d-m-Y');
                $itacaService->goToLlist();
                if ($this->tryOne($itacaService, $falta, $fecha)) {
                    $count++;
                } else {
                    $failures++;
                }
            }

            foreach ($list2 as $falta) {
                $desde = Carbon::parse($falta->desde);
                $hasta = Carbon::parse($falta->hasta);
                $diferenciaDias = $desde->diffInDays($hasta);
                for ($i = 0; $i <= $diferenciaDias; $i++) {
                    $fecha = $desde->copy()->addDays($i)->format('d-m-Y');
                    $itacaService->goToLlist();
                    if ($this->tryOne($itacaService, $falta, $fecha)) {
                        $count++;
                    } else {
                        $failures++;
                    }
                }
                $falta->itaca = 1;
                $falta->save();
            }
        } catch (IntranetException $e) {
            report($e);
            Log::error('Error enviant faltes extraescolars a Itaca.', [
                'errors_actualitzades' => $count ?? null,
                'errors_fallides' => $failures ?? null,
                'error' => $e->getMessage(),
            ]);
            Alert::danger($e->getMessage());
        }

        $itacaService->close();
        Alert::info("$count faltas actualizadas, $failures errores");
        return back();
    }

    /**
     * Intenta registrar una falta concreta en ITACA.
     *
     * @param ItacaService $itacaService
     * @param mixed $falta
     * @param string $fecha
     * @return int
     */
    private function tryOne(ItacaService $itacaService, mixed $falta, string $fecha): int
    {
        try {
            sleep(1);
            $driver = $itacaService->getDriver();
            $itacaService->goToLlist();
            $itacaService->fill(WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'), $falta->idProfesor);
            $itacaService->waitAndClick("//button[contains(text(),'Buscar')]");
            sleep(2);
            $element = $driver->findElement(WebDriverBy::xpath("//div[contains(text(),'{$falta->idProfesor}')]"));
            (new WebDriverActions($driver))->contextClick($element)->perform();
            $itacaService->waitAndClick("//span[contains(text(),'Faltas docente')]");
            $desde = str_replace('-', '/', $fecha);
            sleep(1);
            $itacaService->fill(WebDriverBy::cssSelector('input.z-datebox-input'), $desde);
            $itacaService->waitAndClick(WebDriverBy::xpath('//button[text()="Cambiar Fecha"]'));
            sleep(1);

            if ($falta->dia_completo) {
                $itacaService->waitAndClick(WebDriverBy::xpath('//button[text()="Vista diaria"]'));
                sleep(1);
                $elemento = $driver->findElement(WebDriverBy::cssSelector('.z-calevent-t1'));
                $atributoStyle = $elemento->getAttribute('style');
                $colorFondo = substr($atributoStyle, strpos($atributoStyle, '#') + 1);
                if ($colorFondo === 'ff5d00') {
                    $itacaService->waitAndClick(WebDriverBy::className('z-icon-times'));
                    Alert::info('Falta ja actualizada');
                    $falta->itaca = true;
                    $falta->save();
                } else {
                    $itacaService->waitAndClick(WebDriverBy::xpath('//button[text()="Seleccionar todos"]'));
                    $itacaService->waitAndClick(WebDriverBy::xpath('//button[text()=" Nueva Falta"]'));
                    sleep(2);
                    $checkbox = $driver->findElement(WebDriverBy::cssSelector("span.z-checkbox[data-id='justificada'] input"));
                    if (!$checkbox->isSelected()) {
                        $checkbox->click();
                    }
                    $input = $driver->findElement(WebDriverBy::cssSelector("span.z-combobox[data-id='cbJustificacion'] input"));
                    $input->sendKeys($falta->motivo);
                    $driver->findElement(WebDriverBy::cssSelector("button.z-button[data-tooltip='Guardar']"))->click();
                    Alert::info('Falta actualizada');
                    $falta->itaca = true;
                    $falta->save();
                }
                return 1;
            }
        } catch (\Exception $e) {
            Alert::danger($e->getMessage());
            return 0;
        }

        return 1;
    }
}
