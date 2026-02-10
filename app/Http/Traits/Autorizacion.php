<?php

namespace Intranet\Http\Traits;

use Illuminate\Http\Request;
use Intranet\Services\General\AutorizacionPrintService;
use Intranet\Services\General\AutorizacionStateService;
use Styde\Html\Facades\Alert;


/**
 * Trait de suport per a controllers amb fluxos d'autorització per estats.
 *
 * Contracte esperat del controller que usa el trait:
 * - `protected string $class`: FQCN del model (ex. Intranet\Entities\Foo).
 * - `protected string $model`: nom curt del model per a config/vistes.
 *
 * El trait només coordina redireccions i missatgeria; la lògica de negoci
 * queda delegada en `AutorizacionStateService` i `AutorizacionPrintService`.
 */
trait Autorizacion
{

    protected $init = 1; //estat quan s'inicialitza
    protected $notFollow = false; // quan pasa alguna cosa du a la pestana final de l'estat
    private ?AutorizacionStateService $autorizacionStateService = null;
    private ?AutorizacionPrintService $autorizacionPrintService = null;

    /**
     * Resol i memoitza el servei de transicions d'estat per al model actual.
     */
    private function getAutorizacionStateService(): AutorizacionStateService
    {
        if (!$this->autorizacionStateService) {
            $this->autorizacionStateService = app()->makeWith(
                AutorizacionStateService::class,
                ['class' => $this->class]
            );
        }

        return $this->autorizacionStateService;
    }

    /**
     * Resol i memoitza el servei d'impressió en lot.
     */
    private function getAutorizacionPrintService(): AutorizacionPrintService
    {
        if (!$this->autorizacionPrintService) {
            $this->autorizacionPrintService = app(AutorizacionPrintService::class);
        }

        return $this->autorizacionPrintService;
    }
    
    // cancela pasa a estat -1
    protected function cancel($id)
    {
        if (!$this->getAutorizacionStateService()->cancel($id)) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        return back();
    }
    
    //inicializat a init (normalment 1)
    protected function init($id)
    {
        if (!$this->getAutorizacionStateService()->init($id, (int) $this->init)) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        return back();
    }
    
    //imprimeix
    protected function _print($id)
    {
        if (!$this->getAutorizacionStateService()->print($id)) {
            return back()->with('error', 'Error en imprimir el document.');
        }
    }


    protected function resolve(Request $request, $id, $redirect = true)
    {
        $result = $this->getAutorizacionStateService()->resolve($id, $request->explicacion);

        if ($result === false) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        if ($redirect) {
            return $this->follow($result['initial'], $result['final']);
        }
    }

    // estat + 1
    protected function accept($id, $redirect = true)
    {
        $result = $this->getAutorizacionStateService()->accept($id);

        if ($result === false) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        if ($redirect) {
            return $this->follow($result['initial'], $result['final']);
        }
    }


    // estat -1
    protected function resign($id, $redirect = true)
    {
        $result = $this->getAutorizacionStateService()->resign($id);

        if ($result === false) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        if ($redirect) {
            return $this->follow($result['initial'], $result['final']);
        }
    }

    // refusa
    protected function refuse(Request $request, $id, $redirect = true)
    {
        $result = $this->getAutorizacionStateService()->refuse($id, $request->explicacion);

        if ($result === false) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        if ($redirect) {
            return $this->follow($result['initial'], $result['final']);
        }
    }


    
    // rediriguix o no a un altra pestana
    /**
     * Tria la pestanya de retorn segons `notFollow`.
     */
    private function follow($inicial, $final)
    {
        return $this->notFollow ? back()->with('pestana', $inicial) : back()->with('pestana', $final);
    }


    /**
     * Genera un PDF en lot per als elements en estat inicial i aplica transició.
     *
     * @param string $modelo Vista de PDF sense prefix (`pdf.`). Si va buit, usa
     *                       `strtolower($this->model) . 's'`.
     * @param int|null $inicial Estat de filtratge inicial.
     * @param string|null $final Acció final de `StateService` o estat numèric.
     * @param string $orientacion Orientació del PDF (`portrait|landscape`).
     * @param bool $link Si és `true`, enllaça elements al document generat.
     */
    public function imprimir($modelo = '', $inicial = null, $final = null, $orientacion='portrait', $link=true)
    {
        $response = $this->getAutorizacionPrintService()->imprimir(
            $this->class,
            $this->model,
            $modelo,
            $inicial,
            $final,
            $orientacion,
            $link
        );

        if ($response) {
            return $response;
        }

        Alert::info(trans('messages.generic.empty'));
        return back();
    }



}
