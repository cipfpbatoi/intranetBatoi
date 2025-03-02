<?php

namespace Intranet\Http\Traits;

use Illuminate\Http\Request;
use Intranet\Componentes\Pdf;
use Intranet\Services\GestorService;
use Intranet\Services\StateService;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;


trait Autorizacion
{

    protected $init = 1; //estat quan s'inicialitza
    protected $notFollow = false; // quan pasa alguna cosa du a la pestana final de l'estat
    
    // cancela pasa a estat -1
    protected function cancel($id)
    {
        $stSrv = new StateService($this->class, $id);
        $stSrv->putEstado(-1);
        return back();
    }
    
    //inicializat a init (normalment 1)
    protected function init($id)
    {
        $stSrv = new StateService($this->class, $id);
        $stSrv->putEstado(1);

        return back();
    }
    
    //imprimeix
    protected function _print($id)
    {
        $stSrv = new StateService($this->class, $id);
        $result = $stSrv->_print();

        if ($result === false) {
            return back()->with('error', 'Error en imprimir el document.');
        }
    }


    protected function resolve(Request $request, $id, $redirect = true)
    {
        $stSrv = new StateService($this->class, $id);
        $iniSta = $stSrv->getEstado();
        $finSta = $stSrv->resolve($request->explicacion);

        if ($finSta === false) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        if ($redirect) {
            return $this->follow($iniSta, $finSta);
        }
    }

    // estat + 1
    protected function accept($id, $redirect = true)
    {
        $stSrv = new StateService($this->class, $id);
        $iniSta = $stSrv->getEstado();
        $finSta = $stSrv->putEstado($iniSta + 1);

        if ($finSta === false) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        if ($redirect) {
            return $this->follow($iniSta, $finSta);
        }
    }


    // estat -1
    protected function resign($id, $redirect = true)
    {
        $stSrv = new StateService($this->class, $id);
        $iniSta = $stSrv->getEstado();
        $finSta = $stSrv->putEstado($iniSta-1);

        if ($finSta === false) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        if ($redirect) {
            return $this->follow($iniSta, $finSta);
        }
    }

    // refusa
    protected function refuse(Request $request, $id, $redirect = true)
    {
        $stSrv = new StateService($this->class, $id);
        $iniSta = $stSrv->getEstado();
        $finSta = $stSrv->refuse($request->explicacion);

        if ($finSta === false) {
            return back()->with('error', 'No s\'ha pogut actualitzar l\'estat.');
        }

        if ($redirect) {
            return $this->follow($iniSta, $finSta);
        }
    }


    
    // rediriguix o no a un altra pestana
    private function follow($inicial, $final)
    {
        return $this->notFollow ? back()->with('pestana', $inicial) : back()->with('pestana', $final);
    }


    /**
     * @param string $modelo
     * @param null $inicial
     * @param null $final
     * @param string $orientacion
     * @param bool $link
     * @return \Illuminate\Http\RedirectResponse
     */

    public function imprimir($modelo = '', $inicial = null, $final = null, $orientacion='portrait', $link=true)
    {
        $modelo = $modelo ?? strtolower($this->model) . 's';
        $final = $final ?? '_print';
        $inicial =  $inicial ?? config('modelos.' . getClass($this->class) . '.print') - 1;

        $todos = $this->class::where('estado', '=', $inicial)->get();

        if ($todos->count()) {
            // Generem el PDF
            $pdf = Pdf::hazPdf("pdf.$modelo", $todos, null, $orientacion);

            // Nom del fitxer
            $nom = $this->model . new Date() . '.pdf';
            $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
            $tags = config("modelos.$this->model.documento");

            // Guardem el document al gestor documental
            $doc = GestorService::saveDocument($nomComplet, $tags);

            // Modifiquem l'estat de tots els elements
            StateService::makeAll($todos, $final);

            // Enllacem els elements amb el document si cal
            if ($link) {
                StateService::makeLink($todos, $doc);
            }

            return $pdf->save(storage_path('/app/' . $nomComplet))->download($nom);
        }

        Alert::info(trans('messages.generic.empty'));
        return back();
    }



}
