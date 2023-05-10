<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Componentes\Pdf;
use Intranet\Services\GestorService;
use Intranet\Services\StateService;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;


trait traitAutorizar
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
        $stSrv->_print();
    }


    protected function resolve(Request $request, $id, $redirect = true)
    {
        $stSrv = new StateService($this->class, $id);
        $iniSta = $stSrv->getEstado();
        $finSta = $stSrv->resolve($request->explicacion);
        if ($redirect) {
            return $this->follow($iniSta, $finSta);
        }
    }

    // estat + 1
    protected function accept($id, $redirect = true)
    {
        $stSrv = new StateService($this->class, $id);
        $iniSta = $stSrv->getEstado();
        $finSta = $stSrv->putEstado($iniSta+1);
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
        if ($redirect) {
            return $this->follow($iniSta, $finSta);
        }
    }


    
    // rediriguix o no a un altra pestana
    private function follow($inicial, $final)
    {
        return $this->notFollow ? back()->with('pestana', $inicial) : back()->with('pestana', $final);
    }

    
    //efectuar sobre tots una acció
    protected function makeAll($todos, $accion)
    {
        if (is_string($accion)) {
            foreach ($todos as $uno) {
                $stSrv = new StateService($uno);
                $stSrv->$accion(false);
            }
        } else {
            foreach ($todos as $uno) {
                $stSrv = new StateService($uno);
                $stSrv->putEstado($accion);
            }
        }
    }
    
    //crea link a gestor documental
    protected static function makeLink($todos, $doc)
    {
        foreach ($todos as $uno) {
            $uno->idDocumento = $doc;
            $uno->save();
         }
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
        if ($todos->Count()) {
            $pdf = Pdf::hazPdf("pdf.$modelo", $todos, null, $orientacion);
            $nom = $this->model . new Date() . '.pdf';
            $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
            $tags = config("modelos.$this->model.documento");
            $gestor = new GestorService();
            $doc = $gestor->save(['fichero' => $nomComplet, 'tags' => $tags ]);
            $this->makeAll($todos, $final);
            if ($link) {
                $this->makeLink($todos, $doc);
            }
            return $pdf->save(storage_path('/app/' . $nomComplet))->download($nom);
        }
        Alert::info(trans('messages.generic.empty'));
        return back();

    }
    

}
