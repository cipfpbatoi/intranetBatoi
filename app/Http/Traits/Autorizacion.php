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
    /**
     * Estat inicial per defecte quan s'executa `init()`.
     *
     * @var int
     */
    protected $init = 1;

    /**
     * Si és `true`, manté la pestanya d'origen en lloc de la final.
     *
     * @var bool
     */
    protected $notFollow = false;

    /**
     * Instància memoitzada del servei de transicions.
     */
    private ?AutorizacionStateService $autorizacionStateService = null;

    /**
     * Instància memoitzada del servei d'impressió.
     */
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
    
    /**
     * Mou un element a estat de cancel·lació (`-1`).
     *
     * @param int|string $id Identificador de l'element.
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function cancel($id)
    {
        if (!$this->getAutorizacionStateService()->cancel($id)) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        return back();
    }
    
    /**
     * Inicialitza un element a l'estat definit en `$this->init`.
     *
     * @param int|string $id Identificador de l'element.
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function init($id)
    {
        if (!$this->getAutorizacionStateService()->init($id, (int) $this->init)) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        return back();
    }
    
    /**
     * Aplica la transició `_print` a un element.
     *
     * @param int|string $id Identificador de l'element.
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function _print($id)
    {
        if (!$this->getAutorizacionStateService()->print($id)) {
            return back()->with('error', 'Error en imprimir el document.');
        }
    }


    /**
     * Resol l'element i opcionalment redirigeix a la pestanya d'estat resultant.
     *
     * @param Request $request Request amb `explicacion` opcional.
     * @param int|string $id Identificador de l'element.
     * @param bool $redirect Si és `true`, aplica `follow()`.
     * @return \Illuminate\Http\RedirectResponse|null
     */
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

    /**
     * Incrementa en una unitat l'estat actual de l'element.
     *
     * @param int|string $id Identificador de l'element.
     * @param bool $redirect Si és `true`, aplica `follow()`.
     * @return \Illuminate\Http\RedirectResponse|null
     */
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


    /**
     * Decrementa en una unitat l'estat actual de l'element.
     *
     * @param int|string $id Identificador de l'element.
     * @param bool $redirect Si és `true`, aplica `follow()`.
     * @return \Illuminate\Http\RedirectResponse|null
     */
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

    /**
     * Refusa l'element amb explicació opcional.
     *
     * @param Request $request Request amb `explicacion` opcional.
     * @param int|string $id Identificador de l'element.
     * @param bool $redirect Si és `true`, aplica `follow()`.
     * @return \Illuminate\Http\RedirectResponse|null
     */
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


    
    /**
     * Tria la pestanya de retorn segons `notFollow`.
     *
     * @param int|string|null $inicial Pestanya inicial.
     * @param int|string|null $final Pestanya final.
     * @return \Illuminate\Http\RedirectResponse
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
     * @return mixed
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
